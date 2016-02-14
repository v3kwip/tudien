<?php

namespace tudien;

use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\Provider\ServiceControllerServiceProvider;
use tudien\command\OxfordLDTermCrawlerCommand;
use tudien\controller\TermController;

class App extends Application
{
    const NAME = 'Dictionary';

    public function __construct(array $values)
    {
        parent::__construct($values);

        // Service providers
        $this
            ->register(new DoctrineOrmServiceProvider())
            ->register(new ServiceControllerServiceProvider());

        // Commands
        $this['cmd.oxford-learners-dictionaries.crawler'] = function () {
            return new OxfordLDTermCrawlerCommand();
        };

        // The REST endpoints.
        $this->mount('/term', $this->defineTermResources());
    }

    /**
     * @return ControllerCollection
     */
    private function defineTermResources()
    {
        $this['ctrl.term'] = function () {
            return new TermController();
        };

        /** @var ControllerCollection $routes */
        $routes = $this['controllers_factory'];

        $routes->get('/', 'ctrl.term:index');
        $routes->get('/{slug}', 'ctrl.term:get');

        return $routes;
    }
}
