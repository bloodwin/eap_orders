<?
	/*Шаблон для отображения содержимого отдельного заказа,
	/*Данный шаблон можно разместить в папке используемого шаблона /wp-content/wp-recall/templates/ и он будет подключаться оттуда*/
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
            <td colspan="4"><?php $eap_order->userdata->getFIO(); ?></td> 
        </tr>
        <tr>
        <td colspan="4"><?php $eap_order->userdata->getFullAddress(); ?></td> 
        </tr>
        <tr>
            <td colspan="2"><?php $eap_order->userdata->getEmail(); ?></td> 
            <td colspan="2"><?php $eap_order->userdata->getPhone(); ?></td> 
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
            <td><?php $eap_order->getCreated(); ?></td> 
            <td><?php $eap_order->getStatus(); ?></td> 
            <td><?php $eap_order->getStatusDate(); ?></td> 
            <td><?php $eap_order->getAuthorComment(); ?></td> 
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
		<?php   $basket = $eap_order->getBasket();
                        foreach($basket as $line): ?>
			<tr id="product-<?php $line->getProductId(); ?>">
				<td>
                    <a href="<?php $line->getPermalink(); ?>"><?php $line->getProductName(); ?></a>
                </td>
				<td><?php $line->getProductPrice(); ?></td>
				<td align="center" data-product="<?php $line->getProductId(); ?>">
					<span class="number-product"><?php $line->getProductAmount(); ?></span>
				</td>
				<td class="sumprice-product"><?php $line->getProductTotalPrice(); ?></td>
			</tr>
		<?php endforeach; ?>
		<tr>
			<th colspan="2"></th>
			<th>Общая сумма</th>
                        <th class="cart-summa"><?php $eap_order->getTotalPrice(); ?></th>
		</tr>
	</table>
</div>
