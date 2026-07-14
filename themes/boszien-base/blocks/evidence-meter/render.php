<?php
/**
 * Server-rendered Boszien evidence meter block.
 *
 * @package BoszienBase
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$steps = array(
	array(
		'id'    => 'belofte',
		'title' => __( 'Wat wordt er precies beloofd?', 'boszien-base' ),
		'body'  => __( 'Formuleer de bewering in gewone taal. Gaat het om minder klachten, lager risico, meer energie, sneller herstel of genezing?', 'boszien-base' ),
	),
	array(
		'id'    => 'bewijs',
		'title' => __( 'Waar is het bewijs?', 'boszien-base' ),
		'body'  => __( 'Kijk of de claim steunt op een richtlijn, systematische review, losse studie, expertquote, marketingtekst of mening.', 'boszien-base' ),
	),
	array(
		'id'    => 'doelgroep',
		'title' => __( 'Voor wie geldt dit wel of niet?', 'boszien-base' ),
		'body'  => __( 'Let op doelgroep, uitzonderingen, onzekerheid en situaties waarin de claim te breed wordt toegepast.', 'boszien-base' ),
	),
	array(
		'id'    => 'praktisch',
		'title' => __( 'Wat kun je ermee?', 'boszien-base' ),
		'body'  => __( 'Vertaal de bevinding naar algemene informatie. Geef geen persoonlijke diagnose of behandeling.', 'boszien-base' ),
	),
	array(
		'id'    => 'zorggrens',
		'title' => __( 'Waar stopt online uitleg en begint zorg?', 'boszien-base' ),
		'body'  => __( 'Bij klachten, medicatie, zwangerschap, ernstige signalen of twijfel hoort persoonlijke beoordeling door een passende zorgverlener.', 'boszien-base' ),
	),
);

$context = array(
	'open' => 'belofte',
);

$context_attributes = function_exists( 'wp_interactivity_data_wp_context' )
	? wp_interactivity_data_wp_context( $context )
	: 'data-wp-context="' . esc_attr( wp_json_encode( $context ) ) . '"';
?>

<section
	<?php echo get_block_wrapper_attributes( array( 'class' => 'bz-evidence-meter' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	data-wp-interactive="boszien/evidence-meter"
	<?php echo $context_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
>
	<p class="bz-eyebrow"><?php echo esc_html__( 'Boszien meetlat', 'boszien-base' ); ?></p>
	<h2><?php echo esc_html__( 'Vijf vragen voor bewijskracht.', 'boszien-base' ); ?></h2>
	<p class="bz-evidence-meter__intro">
		<?php echo esc_html__( 'Deze meetlat helpt om een gezondheidsclaim rustig te ontleden. Zonder JavaScript blijven alle toelichtingen zichtbaar in de HTML.', 'boszien-base' ); ?>
	</p>

	<div class="bz-evidence-meter__items">
		<?php foreach ( $steps as $step ) : ?>
			<section class="bz-evidence-meter__item">
				<h3 class="screen-reader-text"><?php echo esc_html( $step['title'] ); ?></h3>
				<button
					type="button"
					class="bz-evidence-meter__button"
					data-step="<?php echo esc_attr( $step['id'] ); ?>"
					data-wp-on--click="actions.toggle"
					data-wp-bind--aria-expanded="callbacks.isOpen"
					aria-controls="bz-evidence-meter-<?php echo esc_attr( $step['id'] ); ?>"
				>
					<span><?php echo esc_html( $step['title'] ); ?></span>
				</button>

				<div
					id="bz-evidence-meter-<?php echo esc_attr( $step['id'] ); ?>"
					class="bz-evidence-meter__panel"
					data-step="<?php echo esc_attr( $step['id'] ); ?>"
					data-wp-bind--hidden="!callbacks.isOpen"
				>
					<p><?php echo esc_html( $step['body'] ); ?></p>
				</div>
			</section>
		<?php endforeach; ?>
	</div>
</section>
