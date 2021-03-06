<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

$enc = $this->encoder();
$watchParams = $this->get( 'watchParams', array() );
$listItems = $this->get( 'watchListItems', array() );
$productItems = $this->get( 'watchProductItems', array() );


/** client/html/account/watch/url/target
 * Destination of the URL where the controller specified in the URL is known
 *
 * The destination can be a page ID like in a content management system or the
 * module of a software development framework. This "target" must contain or know
 * the controller that should be called by the generated URL.
 *
 * @param string Destination of the URL
 * @since 2014.09
 * @category Developer
 * @see client/html/account/watch/url/controller
 * @see client/html/account/watch/url/action
 * @see client/html/account/watch/url/config
 */
$watchTarget = $this->config( 'client/html/account/watch/url/target' );

/** client/html/account/watch/url/controller
 * Name of the controller whose action should be called
 *
 * In Model-View-Controller (MVC) applications, the controller contains the methods
 * that create parts of the output displayed in the generated HTML page. Controller
 * names are usually alpha-numeric.
 *
 * @param string Name of the controller
 * @since 2014.09
 * @category Developer
 * @see client/html/account/watch/url/target
 * @see client/html/account/watch/url/action
 * @see client/html/account/watch/url/config
 */
$watchController = $this->config( 'client/html/account/watch/url/controller', 'account' );

/** client/html/account/watch/url/action
 * Name of the action that should create the output
 *
 * In Model-View-Controller (MVC) applications, actions are the methods of a
 * controller that create parts of the output displayed in the generated HTML page.
 * Action names are usually alpha-numeric.
 *
 * @param string Name of the action
 * @since 2014.09
 * @category Developer
 * @see client/html/account/watch/url/target
 * @see client/html/account/watch/url/controller
 * @see client/html/account/watch/url/config
 */
$watchAction = $this->config( 'client/html/account/watch/url/action', 'watch' );

/** client/html/account/watch/url/config
 * Associative list of configuration options used for generating the URL
 *
 * You can specify additional options as key/value pairs used when generating
 * the URLs, like
 *
 *  client/html/<clientname>/url/config = array( 'absoluteUri' => true )
 *
 * The available key/value pairs depend on the application that embeds the e-commerce
 * framework. This is because the infrastructure of the application is used for
 * generating the URLs. The full list of available config options is referenced
 * in the "see also" section of this page.
 *
 * @param string Associative list of configuration options
 * @since 2014.09
 * @category Developer
 * @see client/html/account/watch/url/target
 * @see client/html/account/watch/url/controller
 * @see client/html/account/watch/url/action
 * @see client/html/url/config
 */
$watchConfig = $this->config( 'client/html/account/watch/url/config', array() );

$detailTarget = $this->config( 'client/html/catalog/detail/url/target' );
$detailController = $this->config( 'client/html/catalog/detail/url/controller', 'catalog' );
$detailAction = $this->config( 'client/html/catalog/detail/url/action', 'detail' );
$detailConfig = $this->config( 'client/html/catalog/detail/url/config', array() );


?>
<section class="aimeos account-watch">

	<?php if( ( $errors = $this->get( 'watchErrorList', array() ) ) !== array() ) : ?>
		<ul class="error-list">
			<?php foreach( $errors as $error ) : ?>
				<li class="error-item"><?php echo $enc->html( $error ); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>


	<?php if( !empty( $listItems ) ) : ?>
		<h2 class="header"><?php echo $this->translate( 'client', 'Watched products' ); ?></h2>

		<ul class="watch-items">
			<?php foreach( $listItems as $listItem ) : $id = $listItem->getRefId(); ?>
				<?php if( isset( $productItems[$id] ) ) : $productItem = $productItems[$id]; ?>
					<?php $prices = $productItem->getRefItems( 'price', null, 'default' ); ?>

					<li class="watch-item">
						<?php $params = array( 'wat_action' => 'delete', 'wat_id' => $id ) + $watchParams; ?>
						<a class="modify" href="<?php echo $this->url( $watchTarget, $watchController, $watchAction, $params, array(), $watchConfig ); ?>">
							<?php echo $this->translate( 'client', 'X' ); ?>
						</a>

						<?php $params = array( 'd_name' => $productItem->getName( 'url' ), 'd_prodid' => $productItem->getId() ); ?>
						<a class="watch-item" href="<?php echo $enc->attr( $this->url( $detailTarget, $detailController, $detailAction, $params, array(), $detailConfig ) ); ?>">
							<?php $mediaItems = $productItem->getRefItems( 'media', 'default', 'default' ); ?>

							<?php if( ( $mediaItem = reset( $mediaItems ) ) !== false ) : ?>
								<div class="media-item" style="background-image: url('<?php echo $this->content( $mediaItem->getPreview() ); ?>')"></div>
							<?php else : ?>
								<div class="media-item"></div>
							<?php endif; ?>

							<h3 class="name"><?php echo $enc->html( $productItem->getName(), $enc::TRUST ); ?></h3>

							<div class="price-list">
								<?php echo $this->partial(
									$this->config( 'client/html/common/partials/price', 'common/partials/price-default.php' ),
									array( 'prices' => $prices )
								); ?>
							</div>
						</a>

						<?php $url = $this->url( $watchTarget, $watchController, $watchAction, $watchParams, array(), $watchConfig ); ?>
						<form class="watch-details" method="POST" action="<?php echo $enc->attr( $url ); ?>">
							<input type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'wat_action' ) ) ); ?>" value="edit" />
							<input type="hidden" name="<?php echo $enc->attr( $this->formparam( array( 'wat_id' ) ) ); ?>" value="<?php echo $enc->attr( $id ); ?>" />
							<?php echo $this->csrf()->formfield(); ?>

							<ul class="form-list">
								<?php $config = $listItem->getConfig(); ?>

								<?php $timeframe = ( isset( $config['timeframe'] ) ? (int) $config['timeframe'] : 7 ); ?>
								<li class="form-item timeframe">
									<label for="watch-timeframe"><?php echo $enc->html( $this->translate( 'client', 'Time frame' ), $enc::TRUST ); ?></label><!--
									--><select id="watch-timeframe" name="<?php echo $enc->attr( $this->formparam( array( 'wat_timeframe' ) ) ); ?>">
										<option value="7" <?php echo ( $timeframe == 7 ? 'selected="selected"' : '' ); ?> >
											<?php echo $enc->html( $this->translate( 'client', 'One week' ) ); ?>
										</option>
										<option value="14" <?php echo ( $timeframe == 14 ? 'selected="selected"' : '' ); ?> >
											<?php echo $enc->html( $this->translate( 'client', 'Two weeks' ) ); ?>
										</option>
										<option value="30" <?php echo ( $timeframe == 30 ? 'selected="selected"' : '' ); ?> >
											<?php echo $enc->html( $this->translate( 'client', 'One month' ) ); ?>
										</option>
										<option value="90" <?php echo ( $timeframe == 90 ? 'selected="selected"' : '' ); ?> >
											<?php echo $enc->html( $this->translate( 'client', 'Three month' ) ); ?>
										</option>
									</select>
								</li>

								<?php $price = ( isset( $config['price'] ) ? (int) $config['price'] : 0 ); ?>
								<li class="form-item price">
									<label for="watch-price"><?php echo $enc->html( $this->translate( 'client', 'If price decreases' ), $enc::TRUST ); ?></label><!--
									--><input type="checkbox"
										name="<?php echo $enc->attr( $this->formparam( array( 'wat_price' ) ) ); ?>"
										id="watch-price"
										value="1"
										<?php echo ( $price ? 'checked="checked"' : '' ); ?>
									/>
									<input type="hidden"
										name="<?php echo $enc->attr( $this->formparam( array( 'wat_pricevalue' ) ) ); ?>"
										value="<?php echo $enc->attr( ( $priceItem = reset( $prices ) ) !== false ? $priceItem->getValue() : '0.00' ); ?>"
									/>
								</li>

								<?php $stock = ( isset( $config['stock'] ) ? (int) $config['stock'] : 0 ); ?>
								<li class="form-item stock">
									<label for="watch-stock"><?php echo $enc->html( $this->translate( 'client', 'If back in stock' ), $enc::TRUST ); ?></label><!--
										--><input type="checkbox"
											name="<?php echo $enc->attr( $this->formparam( array( 'wat_stock' ) ) ); ?>"
											id="watch-stock"
											value="1"
											<?php echo ( $stock ? 'checked="checked"' : '' ); ?>
										/>
								</li>
							</ul>

							<div class="button-group">
								<button class="standardbutton btn-action"><?php echo $enc->html( $this->translate( 'client', 'Watch' ), $enc::TRUST ); ?></button>
							</div>
						</form>

					</li>

				<?php endif; ?>
			<?php endforeach; ?>
		</ul>


		<?php if( $this->get( 'watchPageLast', 1 ) > 1 ) : ?>
			<nav class="pagination">
				<div class="sort">
					<span>&nbsp;</span>
				</div>
				<div class="browser">

					<?php $params = array( 'wat_page' => $this->watchPageFirst ) + $watchParams; ?>
					<a class="first" href="<?php echo $enc->attr( $this->url( $watchTarget, $watchController, $watchAction, $params, array(), $watchConfig ) ); ?>">
						<?php echo $enc->html( $this->translate( 'client', '◀◀' ), $enc::TRUST ); ?>
					</a>

					<?php $params = array( 'wat_page' => $this->watchPagePrev ) + $watchParams; ?>
					<a class="prev" href="<?php echo $enc->attr( $this->url( $watchTarget, $watchController, $watchAction, $params, array(), $watchConfig ) ); ?>" rel="prev">
						<?php echo $enc->html( $this->translate( 'client', '◀' ), $enc::TRUST ); ?>
					</a>

					<span>
						<?php echo $enc->html( sprintf(
							$this->translate( 'client', 'Page %1$d of %2$d' ),
							$this->get( 'watchPageCurr', 1 ),
							$this->get( 'watchPageLast', 1 )
						) ); ?>
					</span>

					<?php $params = array( 'wat_page' => $this->watchPageNext ) + $watchParams; ?>
					<a class="next" href="<?php echo $enc->attr( $this->url( $watchTarget, $watchController, $watchAction, $params, array(), $watchConfig ) ); ?>" rel="next">
						<?php echo $enc->html( $this->translate( 'client', '▶' ), $enc::TRUST ); ?>
					</a>

					<?php $params = array( 'wat_page' => $this->watchPageLast ) + $watchParams; ?>
					<a class="last" href="<?php echo $enc->attr( $this->url( $watchTarget, $watchController, $watchAction, $params, array(), $watchConfig ) ); ?>">
						<?php echo $enc->html( $this->translate( 'client', '▶▶' ), $enc::TRUST ); ?>
					</a>
				</div>
			</nav>
		<?php endif; ?>

	<?php endif; ?>
</section>
