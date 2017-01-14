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
            <td>Printful Code</td>
            <td>Images</td>
        </tr>
    </thead>
    <?php foreach ($order->getItems() as $item): ?>
    <tr>
        <td><input type="checkbox" checked="checked" name="checkout_order_item[<?= $item['checkout_order_item_id']; ?>" value="1" /></td>
        <td><?= $item['title']; ?></td>
        <td><?= $item['options_formatted']; ?></td>
        <td><?= $item['product']->getAggregateOption('printful_product', $item); ?></td>
        <td><?= $item['product']->getAggregateOption('printful_image', $item); ?></td>
    </tr>
    <?php endforeach; ?>
</table>
    <input type="submit" name="submit" value="Fulfill Order" class="button medium">
</form>
