<?php

namespace Wordlift\Api;

interface Api_Service_Ext {
	/**
	 * @return Me_Response
	 */
	public function me();
}

/**
 *
 * @property int $account_id;
 * @property int $dataset_id;
 * @property string $dataset_uri;
 * @property array<string, bool> $features;
 * @property string $language;
 * @property null|Network[] $networks;
 * @property int $subscription_id;
 * @property string $url;
 */
// phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound
interface Me_Response {
}

/**
 * @property int $account_id
 * @property int $dataset_id
 * @property string $dataset_uri
 * @property string $url
 */
// phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound
interface Network {
}
