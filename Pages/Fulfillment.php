<?php

namespace Modules\ThePrintful\Pages;

use Exception;
use Lightning\Tools\Messenger;
use Lightning\Tools\Navigation;
use Lightning\View\Page;
use Lightning\Tools\ClientUser;
use Lightning\Tools\Communicator\RestClient;
use Lightning\Tools\Configuration;
use Lightning\Tools\Request;
use Lightning\Tools\Template;
use Modules\Checkout\Model\LineItem;
use Modules\Checkout\Model\Order;

class Fulfillment extends Page {

    protected $rightColumn = false;
    protected $page = ['fulfillment', 'ThePrintful'];
    protected $share = false;

    public function hasAccess() {
        return ClientUser::requireAdmin();
    }

    public function get() {
        $order = Order::loadByID(Request::get('id', Request::TYPE_INT));
        Template::getInstance()->set('order', $order);
    }

    public function post() {
        $order = Order::loadByID(Request::post('id', Request::TYPE_INT));
        if (empty($order)) {
            throw new Exception('Could not load order.');
        }

        // Prepare the shipping address.
        $address = $order->getShippingAddress();
        if (empty($address)) {
            throw new Exception('Could not load address.');
        }
        $recipient = [
            'name' => $address->name,
            'address1' => $address->street,
            'address2' => $address->street2,
            'city' => $address->city,
            'state_code' => $address->state,
            'country_code' => $address->country,
            'zip' => $address->zip,
        ];

        // Figure out white items to ship.
        $items = $itemObjects = [];
        foreach ($order->getItemsToFulfillWithHandler('printful') as $item) {
            /* @var LineItem $item */

            // Prepare the images.
            $images = $item->getAggregateOption('printful_image');
            if (empty($images)) {
                throw new Exception('Printful images not configured.');
            }
            $image_array = [];
            if (!is_array($images)) {
                $images = [$images];
            }
            foreach ($images as $i) {
                // TODO: This should be able to handle images that are saved with metadata.
                $image_array[] = ['id' => $i];
            }

            // Prepare the rest of the item.
            $items[] = [
                // Must be unique for each item shipped.
                'external_id' => $item->id,
                // Description of printful product, including size and color.
                'variant_id' => $item->getAggregateOption('printful_product'),
                'quantity' => $item->qty,
                'files' => $image_array,
            ];
            $itemObjects[] = $item;
        }

        if (empty($items)) {
            throw new Exception('No items to ship.');
        }

        // Send to printful.
        $client = new RestClient('https://api.theprintful.com/');
        $client->sendJSON(true);

        $client->setBasicAuth(
            Configuration::get('modules.theprintful.api_user'),
            Configuration::get('modules.theprintful.api_password'));
        $client->set('external_id', $order->order_id);
        $client->set('recipient', $recipient);
        $client->set('items', $items);
        if ($client->callPost('/orders')) {
            foreach ($itemObjects as $item) {
                $item->markFulfilled();
            }
            $order->markFullfilled();
            Messenger::message('The order has been processed.');
            Navigation::redirect('/admin/orders?id=' . $order->id);
        } else {
            throw new Exception('There was a problem submitting the order.');
        }
    }
}
