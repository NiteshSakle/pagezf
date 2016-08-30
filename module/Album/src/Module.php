<?php
namespace Album;

use Album\Controller\AlbumController;
use Album\Model\Album;
use Album\Model\AlbumTable;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
	
	public function getServiceConfig()
    {
        return [
            'factories' => [
                AlbumTable::class => function($container) {
            
                    $tableGateway = $container->get(Model\AlbumTableGateway::class);
                    return new AlbumTable($tableGateway);
                },
                Model\AlbumTableGateway::class => function ($container) {
                    
                    $dbAdapter = $container->get(AdapterInterface::class);
                    
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Album());
                    return new TableGateway('album', $dbAdapter, null, $resultSetPrototype);
                },
            ],
        ];
    }

	 public function getControllerConfig()
    {
        return [
            'factories' => [
                AlbumController::class => function($container) {
                    
                    return new AlbumController(
                        $container->get(AlbumTable::class)
                    );
                },
            ],
        ];
    }
}