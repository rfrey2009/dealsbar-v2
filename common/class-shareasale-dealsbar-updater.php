<?php
class ShareASale_Dealsbar_Updater {
	/**
	* @var Wpdb $wpdb WordPress global database connection singleton
	* @var float $version Plugin version, used for cache-busting
	*/
	private $wpdb, $version;

	public function __construct( $version ) {
		$this->version = $version;
		$this->load_dependencies();
	}

	private function load_dependencies() {
		require_once plugin_dir_path( __FILE__ ) . '../common/class-shareasale-dealsbar-api.php';
		global $wpdb;

		$this->wpdb = &$wpdb;
	}

	public function update() {
		$options = get_option( 'dealsbar_options' );

		$table = $this->wpdb->prefix . 'deals';
		$this->wpdb->query( 'TRUNCATE TABLE ' . $table ); //empty table before adding deals so only current, joined merchants deals remain

		$shareasale_api = new ShareASale_Dealsbar_API( $options['affiliate-id'], $options['api-token'], $options['api-secret'] );
		$deals          = $shareasale_api->coupon_deals( array( 'current' => 1 ) )->exec();

		if ( false != $deals ) {
			foreach ( $deals->dealcouponlistreportrecord as $deal ) {
				$values = array(
					'dealid' => $deal->dealid,
					'merchantid' => $deal->merchantid,
					'merchant' => $deal->merchant,
					'startdate' => $deal->startdate,
					'enddate' => $deal->enddate,
					'publishdate' => $deal->publishdate,
					'title' => $deal->title,
					'bigimage' => $deal->bigimage,
					'trackingurl' => $deal->trackingurl,
					'smallimage' => $deal->smallimage,
					'category' => $deal->category,
					'description' => $deal->description,
					'restrictions' => $deal->restrictions,
					'keywords' => $deal->keywords,
					'couponcode' => $deal->couponcode,
					'editdate' => $deal->editdate,
				);

				$this->wpdb->insert( $table, $values );
			}
		}
	}
}
