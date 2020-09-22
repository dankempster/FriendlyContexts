<?php


use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\MinkExtension\Context\MinkContext;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Knp\FriendlyContexts\Context\Context;

class FeatureContext extends Context
{
    /**
     * @var MinkContext
     */
    protected $minkContext;

    /**
     * @BeforeScenario
     * @param BeforeScenarioScope $scope
     */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->minkContext = $environment->getContext('Behat\MinkExtension\Context\MinkContext');
    }

    /**
     * @BeforeScenario
     */
    public function createDatabase()
    {
        /** @var ObjectManager[] $managers */
        $managers = $this->get('doctrine')->getManagers();

        foreach ($managers as $manager) {
            if ($manager instanceof EntityManagerInterface) {
                $schemaTool = new SchemaTool($manager);
                $schemaTool->dropDatabase();
                $schemaTool->createSchema(
                    $manager->getMetadataFactory()->getAllMetadata()
                );
            }
        }
    }

    /**
     * @Then debug page HTML
     */
    public function debugPageHtml()
    {
        file_put_contents("/home/travis/travis-debug.html", $this->minkContext->getSession()->getPage()->getHtml());
        echo "Page: " . $this->minkContext->getSession()->getPage()->getHtml();
    }

}