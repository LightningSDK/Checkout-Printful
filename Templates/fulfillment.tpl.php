<h2>Ship to:</h2>
<?= $order->getShippingAddress()->getHTMLFormatted(); ?>

<h2>Include Items:</h2>
<form method="post">
    <?= \Lightning\Tools\Form::renderTokenInput(); ?>
    <input type="hidden" name="id" value="<?= $order->id; ?>">
<table width="100%">
    <thead>
        <tr>
            <td><input type="checkbox" checked="checked"></td>
            <td>Product</td>
            <td>Options</td>
            <td>Printful Product Code</td>
            <td>Imprint Data</td>
        </tr>
    </thead>
    <?php foreach ($order->getItemsToFulfillWithHandler('printful') as $item): ?>
    <tr>
        <td><input type="checkbox" checked="checked" name="checkout_order_item[<?= $item->id; ?>" value="1" /></td>
        <td><a href="<?= $item->getProduct()->getURL(); ?>"><?= $item->getProduct()->title; ?></a></td>
        <td><?= $item->getHTMLFormattedOptions(); ?></td>
        <td><?= $item->getAggregateOption('printful_product'); ?></td>
        <td><?= json_encode($item->getAggregateOption('printful_image')); ?></td>
    </tr>
    <?php endforeach; ?>
</table>
    <input type="submit" name="submit" value="Fulfill Order" class="button medium">
</form>
