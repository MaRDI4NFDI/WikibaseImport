<?php

namespace Wikibase\Import;

use DataValues\Serializers\DataValueSerializer;
use LoadBalancer;
use Psr\Log\LoggerInterface;
use Wikibase\DataModel\Deserializers\DeserializerFactory;
use Wikibase\DataModel\Serializers\SerializerFactory;
use Wikibase\Lib\Store\EntityStore;
use Wikibase\Import\Store\DBImportedEntityMappingStore;
use Wikibase\Import\Store\ImportedEntityMappingStore;
use Wikibase\Repo\WikibaseRepo;

class EntityImporterFactory {

	/**
	 * @var EntityStore
	 */
	private $entityStore;

	/**
	 * @var LoadBalancer
	 */
	private $loadBalancer;

	/**
	 * @var ImportedEntityMappingStore
	 */
	private $importedEntityMappingStore;

	/**
	 * @var LoggerInterface
	 */
	private $logger;

	/**
	 * @var string
	 */
	private $apiUrl;

	private $entityImporter = null;

	/**
	 * @param EntityStore $entityStore
	 * @param LoadBalancer $loadBalancer
	 * @param LoggerInterface $logger
	 * @param string $apiUrl
	 */
	public function __construct(
		EntityStore $entityStore,
		LoadBalancer $loadBalancer,
		LoggerInterface $logger,
		$apiUrl
	) {
		$this->entityStore = $entityStore;
		$this->loadBalancer = $loadBalancer;
		$this->logger = $logger;
		$this->apiUrl = $apiUrl;
	}

	/**
	 * @return EntityImporter
	 */
	public function newEntityImporter() {
		if ( $this->entityImporter === null ) {
			$this->entityImporter = new EntityImporter(
				$this->newStatementsImporter(),
				$this->newBadgeItemUpdater(),
				$this->getApiEntityLookup(),
				$this->entityStore,
				$this->getImportedEntityMappingStore(),
				new PagePropsStatementCountLookup( $this->loadBalancer, $this->getEntityNamespaceLookup() ),
				$this->logger
			);
		}

		return $this->entityImporter;
	}

	/**
	 * @return ApiEntityLookup
	 */
	public function getApiEntityLookup() {
		return new ApiEntityLookup(
			$this->newEntityDeserializer(),
			$this->logger,
			$this->apiUrl
		);
	}

	private function newBadgeItemUpdater() {
		return new BadgeItemUpdater( $this->getImportedEntityMappingStore() );
	}

	private function newStatementsImporter() {
		return new StatementsImporter(
			$this->newSerializerFactory()->newStatementSerializer(),
			$this->getImportedEntityMappingStore(),
			$this->logger
		);
	}

	private function getImportedEntityMappingStore() {
		if ( $this->importedEntityMappingStore === null ) {

			$this->importedEntityMappingStore = new DBImportedEntityMappingStore(
				$this->loadBalancer,
				WikibaseRepo::getEntityIdParser()
			);
		}

		return $this->importedEntityMappingStore;
	}

	private function newEntityDeserializer() {

		$deserializerFactory = WikibaseRepo::getBaseDataModelDeserializerFactory();

		return $deserializerFactory->newEntityDeserializer();
	}

	private function newSerializerFactory() {
		return new SerializerFactory(
			new DataValueSerializer()
		);
	}

	private function getEntityNamespaceLookup() {

		return WikibaseRepo::getEntityNamespaceLookup();
	}
}

$maintClass = "Wikibase\Import\Maintenance\ImportEntities";
require_once RUN_MAINTENANCE_IF_MAIN;
