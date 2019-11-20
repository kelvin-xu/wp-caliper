<?php
namespace WPCaliperPlugin\caliper;

use WPCaliperPlugin\WP_Caliper;
use WPCaliperPlugin\caliper\ResourceIRI;
use WPCaliperPlugin\caliper\CaliperEvent;

use IMSGlobal\Caliper\Options;
use IMSGlobal\Caliper\Sensor;
use IMSGlobal\Caliper\events\Event;
use IMSGlobal\Caliper\Client;
use IMSGlobal\Caliper\util\TimestampUtil;

use IMSGlobal\Caliper\request\HttpRequestor;

/**
 * Handles the Caliper Sensor
 */
class CaliperSensor {
	/**
	 * Stores the sensor options (api key/endpoint)
	 */
	private static $options = null;
	/**
	 * Enable/Disable sending events (for testing)
	 */
	private static $send_events = true; // allows disabling for unit tests.
	/**
	 * Stores the sent Caliper events (for testing)
	 */
	private static $temp_store = array();

	/**
	 * Sets $send_events (for testing)
	 */
	public static function set_send_events( $send_events ) {
		self::$send_events = $send_events;
	}

	/**
	 * Gets the envelopes (for testing)
	 */
	public static function get_envelopes() {
		$envelopes        = self::$temp_store;
		self::$temp_store = array();
		return $envelopes;
	}

	/**
	 * Set the Caliepr sensor api key/host
	 */
	public static function set_options( $host, $api_key ) {
		self::$options = ( new Options() )
			->setApiKey( "Bearer $api_key" )
			->setHost( $host );
	}

	/**
	 * Get the Caliepr sensor options
	 */
	private static function get_options() {
		return self::$options;
	}

	/**
	 * Get the Caliepr sensor
	 */
	private static function get_sensor() {
		$sensor = new Sensor( ResourceIRI::word_press() );
		$sensor->registerClient(
			'default_client', new Client( 'remote_lrs', self::get_options() )
		);
		return $sensor;
	}

	/**
	 * Checks if Caliper is enabled
	 */
	public static function caliper_enabled() {
		$options = self::get_options();
		return null !== $options && is_string( $options->getHost() ) && is_string( $options->getApiKey() );
	}

	/**
	 * Generates the envelop and tries to send the event via _send_event
	 * If it fails, it will add it the the retry queue
	 */
	public static function send_event( Event &$event, \WP_User $user ) {
		if ( ! self::caliper_enabled() ) {
			return false;
		}
		CaliperEvent::add_defaults( $event, $user );

		$requestor  = new HttpRequestor( self::get_options() );
		$envelope   = $requestor->createEnvelope( self::get_sensor(), $event );
		$event_json = $requestor->serializeData( $envelope );

		$success = self::_send_event( $event_json );
		if ( ! $success ) {
			$blog_id = function_exists( 'get_current_blog_id' ) ? get_current_blog_id() : null;
			WP_Caliper::wp_caliper_add_queue( $event_json, $blog_id );
		}
		return $success;
	}

	/**
	 * Sends a Caliper event to endpoint
	 */
	public static function _send_event( $event_json ) {
		if ( ! is_string( $event_json ) ) {
			throw new \InvalidArgumentException( __METHOD__ . ': string expected' );
		}
		if ( ! self::caliper_enabled() ) {
			return false;
		}

		// used for unit tests.
		if ( ! self::$send_events ) {
			self::$temp_store[] = $event_json;
			return true;
		}

		/*
		 * Requires curl extension
		 * based off of https://github.com/IMSGlobal/caliper-php/blob/master/src/request/HttpRequestor.php#L75
		 */
		$client  = curl_init( self::get_options()->getHost() );
		$headers = [
			'Content-Type: application/json',
			'Authorization: ' . self::get_options()->getApiKey(),
		];
		curl_setopt_array(
			$client,
			array(
				CURLOPT_POST           => true,
				CURLOPT_TIMEOUT_MS     => self::get_options()->getConnectionTimeout(),
				CURLOPT_HTTPHEADER     => $headers,
				CURLOPT_USERAGENT      => 'Caliper ( PHP curl extension )',
				CURLOPT_HEADER         => true, // CURLOPT_HEADER required to return response text.
				CURLOPT_RETURNTRANSFER => true, // CURLOPT_RETURNTRANSFER required to return response text.
				CURLOPT_POSTFIELDS     => $event_json,
			)
		);

		$response_text = curl_exec( $client );
		$response_info = curl_getinfo( $client );
		curl_close( $client );

		$response_code = $response_text ? $response_info['http_code'] : null;
		if ( 200 !== $response_code ) {
			error_log( '[wp-caliper] Failed to emit Caliper event: ' . print_r( $event_json, true ) );
			return false;
		}
		return true;
	}
}
