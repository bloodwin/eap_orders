<?
    /** Шаблон для отображения содержимого отдельного заказа,
    * Данный шаблон можно разместить в папке используемого шаблона /wp-content/wp-recall/templates/ 
    * и он будет подключаться оттуда
    */
?>
<?php
    global $wpdb;
    $eap_order = Eap_Order::getInstance($_GET['order-id'], $wpdb, EAP_PREF);
?>
<div id="cart-form" class="cart-data">
    <table bordercolor="сссссс" border="1" cellpadding="5" class="order-data">
        <tr>
            <th colspan="4">Данные получателя</th> 
        </tr>
        <tr>
            <td colspan="4"><?php echo $eap_order->userdata->getFIO(); ?></td> 
        </tr>
        <tr>
            <td colspan="4"><?php echo $eap_order->userdata->getFullAddress(); ?></td> 
        </tr>
        <tr>
            <td colspan="2"><?php echo $eap_order->userdata->getEmail(); ?></td> 
            <td colspan="2"><?php echo $eap_order->userdata->getPhone(); ?></td> 
        </tr>
        <tr>
            <th colspan="4">Данные заказа</th> 
        </tr>
        <tr>
            <th>Создан</th> 
            <th>Статус</th> 
            <th>Статус изменен</th> 
            <th>Комментарий</th> 
        </tr>
        <tr>
            <td><?php echo $eap_order->getCreated(); ?></td> 
            <td><?php echo $eap_order->getStatus(); ?></td> 
            <td><?php echo $eap_order->getStatusDate(); ?></td> 
            <td><?php echo $eap_order->getAuthorComment(); ?></td> 
        </tr>
        <tr>
            <th colspan="4">Состав заказа</th> 
        </tr>
        <tr>
            <th class="product-name">Товар</th>
            <th width="70">Цена</th>
            <th class="product-number">Количество</th>
            <th width="70">Сумма</th>
        </tr>
        <?php   $basket_full = $eap_order->getBasket();
                $basket = $basket_full->getBasket();
                foreach ($basket as $line):
        ?>
            <tr id="product-<?php echo $line->getProductId(); ?>">
                <td>
                    <a href="<?php echo $line->getPermalink(); ?>"><?php echo $line->getProductName(); ?></a>
                </td>
                <td><?php echo $line->getProductPrice(); ?></td>
                <td align="center" data-product="<?php echo $line->getProductId(); ?>">
                    <span class="number-product"><?php echo $line->getProductAmount(); ?></span>
                </td>
                <td class="sumprice-product"><?php echo $line->getProductTotalPrice(); ?></td>
            </tr>
         <?php endforeach; ?>
        <tr>
            <th colspan="2"></th>
            <th>Общая сумма</th>
            <th class="cart-summa"><?php echo $eap_order->getTotalPrice(); ?></th>
        </tr>
    </table>
</div>
