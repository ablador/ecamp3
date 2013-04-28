<?php

namespace EcampCore\RepositoryUtil;

use Zend\Config\Config;

use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\DocBlockGenerator;
use Zend\Code\Generator\DocBlock\Tag\ReturnTag;

use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\ServiceLocatorInterface;

class RepositoryConfigWriter
{
	
	private $repositoryConfig = 
"<?php
return array(
	
	'aliases' => array(
<<REPOSITORY-ALIASES>>
	),

	'factories' => array(
<<REPOSITORY-FACTORY>>
	),
	
);
";
	
	private $repositoryFactory = 
"		'<<REPOSITORY-ALIAS>>' => new EcampCore\RepositoryUtil\RepositoryFactory('<<ENTITY-CLASS>>'),";
	
	private $repositoryAlias = 
"		'__repos__.<<REPOSITORY-GET-METHOD>>' => '<<REPOSITORY-ALIAS>>',";
		
	
	
	/**
	 * @var Zend\ServiceManager\ServiceLocatorInterface
	 */
	private $serviceLocator;
	
	/**
	 * @var Doctrine\ORM\EntityManager
	 */
	private $em;
	
	
	public function __construct(
		ServiceLocatorInterface $serviceLocator,
		EntityManager $em
	){
		$this->serviceLocator = $serviceLocator;
		$this->em = $em;
	}
	
	
	public function writeRepositoryConfigs(){

		$config = new Config($this->serviceLocator->get('config'));
		
		foreach($config->ecamp->modules as $module){
			$this->writeRepositoryConfig(
				$module->repos->module_namespace,
				$module->repos->config_file
			);
		}
		
	}
	
	
	private function writeRepositoryConfig(
			$moduleNamespace,
			$repositoryConfigFile
	){
		$repositoryFactories = array();
		$repositoryAliases = array();
		
		$classMetadataList = $this->em->getMetadataFactory()->getAllMetadata();
		foreach($classMetadataList as $classMetadata){
			
			if( $classMetadata->name != 'EcampCore\Entity\BaseEntity' &&
				substr($classMetadata->name, 0, strlen($moduleNamespace)) == $moduleNamespace
			){
				$entityClass = $classMetadata->name;
				
				$repositoryMethod = str_replace('\Entity\\', '_', $classMetadata->name);
				$repositoryMethod = lcfirst($repositoryMethod) . "Repo";
				
				$repositoryAlias  = str_replace('\\Entity\\', '.repo.', $classMetadata->name);
				$repositoryAlias = strtolower($repositoryAlias);
				
				$repositoryFactories[] = str_replace(
					array('<<ENTITY-CLASS>>', '<<REPOSITORY-ALIAS>>'),
					array($entityClass, $repositoryAlias),
					$this->repositoryFactory);
				
				$repositoryAliases[] = str_replace(
					array('<<REPOSITORY-GET-METHOD>>', '<<REPOSITORY-ALIAS>>'),
					array($repositoryMethod, $repositoryAlias),
					$this->repositoryAlias);
			}
		}
		
		$src = str_replace(
			array("<<REPOSITORY-FACTORY>>", "<<REPOSITORY-ALIASES>>"),
			array(implode(PHP_EOL, $repositoryFactories), implode(PHP_EOL, $repositoryAliases)),
			$this->repositoryConfig);
		
		file_put_contents($repositoryConfigFile, $src);
		
		return $src;
	}
	
}