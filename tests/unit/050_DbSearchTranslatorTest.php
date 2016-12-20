<?php

use Netinteractive\Elegant\Search\Db\Translator AS Translator;

/**
 * Class DbSearchTranslatorTest
 */
class DbSearchTranslatorTest extends  ElegantTest
{
    /**
     * translate test 1.1
     */
    public function testTranslateTextOne()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $dbTranslator = new \Netinteractive\Elegant\Search\Db\Translator();

        $func = $dbTranslator->translate('street', 1);

        $q = $dbMapper->getNewQuery();
        $func($q, 'unknown 1');

        $result = $q->get();

        $this->assertEquals(1, count($result));
    }

    /**
     * translate test 1.2
     */
    public function testTranslateTextOneOr()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $dbTranslator = new \Netinteractive\Elegant\Search\Db\Translator();

        $func = $dbTranslator->translate('street', 1);

        $q = $dbMapper->getNewQuery();
        $q->where('med.id', '=', 2);
        $func($q, 'unknown 1', 'OR');

        $result = $q->get();

        $this->assertEquals(2, count($result));
    }

    /**
     * translate test 2.1
     */
    public function testTranslateTextTwo()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $dbTranslator = new \Netinteractive\Elegant\Search\Db\Translator();

        $func = $dbTranslator->translate('street', 2);

        $q = $dbMapper->getNewQuery();
        $func($q, 'nown 1');

        $result = $q->get();

        $this->assertEquals(1, count($result));
    }

    /**
     * translate test 2.2
     */
    public function testTranslateTextTwoOr()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $dbTranslator = new \Netinteractive\Elegant\Search\Db\Translator();

        $func = $dbTranslator->translate('street', 2);

        $q = $dbMapper->getNewQuery();
        $q->where('med.id', '=', 2);
        $func($q, 'nown 1', 'OR');

        $result = $q->get();

        $this->assertEquals(2, count($result));
    }

    /**
     * translate test 3.1
     */
    public function testTranslateTextTree()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $dbTranslator = new \Netinteractive\Elegant\Search\Db\Translator();

        $func = $dbTranslator->translate('street', 3);

        $q = $dbMapper->getNewQuery();
        $func($q, 'unknown');

        $result = $q->get();

        $this->assertEquals(2, count($result));
    }

    /**
     * translate test 3.2
     */
    public function testTranslateTextTreeOr()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('PatientData');
        $dbTranslator = new \Netinteractive\Elegant\Search\Db\Translator();

        $func = $dbTranslator->translate('first_name', 3);

        $q = $dbMapper->getNewQuery();
        $q->where('patient_data.id', '=', 1);
        $func($q, 'Ad', 'OR');

        $result = $q->get();

        $this->assertEquals(3, count($result));
    }

    /**
     * translate test 4.1
     */
    public function testTranslateStandard()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $dbTranslator = new \Netinteractive\Elegant\Search\Db\Translator();

        $func = $dbTranslator->translate('id', 4);

        $q = $dbMapper->getNewQuery();
        $func($q, '1');

        $result = $q->get();

        $this->assertEquals(1, count($result));
    }

    /**
     * translate test 4.2
     */
    public function testTranslateStandardOr()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $dbTranslator = new \Netinteractive\Elegant\Search\Db\Translator();

        $func = $dbTranslator->translate('id', 4);

        $q = $dbMapper->getNewQuery();
        $q->where('med.id', '=', 2);

        $func($q, '1' , 'Or');

        $result = $q->get();

        $this->assertEquals(2, count($result));
    }

    /**
     * translate test 4.3
     */
    public function testTranslateStandardArray()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('Med');
        $dbTranslator = new \Netinteractive\Elegant\Search\Db\Translator();

        $func = $dbTranslator->translate('id', 4);

        $q = $dbMapper->getNewQuery();
        $func($q, array(1,2));

        $result = $q->get();

        $this->assertEquals(2, count($result));
    }


    /**
     * translate test 4.4
     */
    public function testTranslateStandardArrayOr()
    {
        $dbMapper = new \Netinteractive\Elegant\Repository\Repository('PatientData');
        $dbTranslator = new \Netinteractive\Elegant\Search\Db\Translator();

        $func = $dbTranslator->translate('id', 4);

        $q = $dbMapper->getNewQuery();
        $q->where('last_name', '=', 'Second');
        $func($q, array(1,2), 'OR');

        $result = $q->get();

        $this->assertEquals(3, count($result));
    }

    /**
     * get like operator test 1
     */
    public function testGetLikeOperator()
    {
        $driver = \Config::get('database.default');
        $exptected = 'LIKE';
        if ($driver == 'pgsql'){
            $exptected = 'iLIKE';
        }

        $this->assertEquals($exptected, Translator::getLikeOperator());
    }

    /**
     * clear keyword test 1
     */
    public function testClearKeyword()
    {
        $this->assertEquals('test', Translator::clearKeyword(' test '));
        $this->assertEquals(array(' test '), Translator::clearKeyword( array(' test ')));
    }

    /**
     * prepare field name test 1
     */
    public function testPrepareField()
    {
        Translator::$alias = 'user';
        $this->assertEquals('user.id', Translator::prepareField('id'));
        Translator::$alias = null;
    }


}