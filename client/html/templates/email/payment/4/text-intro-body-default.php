<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$order = $this->extOrderItem;

/// Payment e-mail intro with order ID (%1$s), order date (%2$s) and payment status (%3%s)
$msg = $this->translate( 'client', 'Thank you for your order %1$s from %2$s.' );
$msg2 = $this->translate( 'client', 'The order is pending until we receive the final payment. If you\'ve chosen to pay in advance, please transfer the money to our bank account with the order ID %1$s as reference.' );

$key = 'pay:' . $order->getPaymentStatus();
$status = $this->translate( 'client/code', $key );
$format = $this->translate( 'client', 'Y-m-d' );

$intro = sprintf( $msg, $order->getId(), date_create( $order->getTimeCreated() )->format( $format ), $status );
$details = sprintf( $msg2, $order->getId(), date_create( $order->getTimeCreated() )->format( $format ), $status );


?>
<?php $this->block()->start( 'email/payment/text/intro' ); ?>


<?php echo wordwrap( strip_tags( $intro ) ); ?>


<?php echo wordwrap( strip_tags( $details ) ); ?>
<?php $this->block()->stop(); ?>
<?php echo $this->block()->get( 'email/payment/text/intro' ); ?>
