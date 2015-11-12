<?php



class DbMapperTest extends ElegantTest
{

    /**
     * Single field PK find test
     */
    public function testFindSimplePk()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Patient');
        $record = $dbMapper->find(1);

        $this->assertEquals(1,$record->id);
    }


    /**
     * Multiple field PK find test
     */
    public function testFindMultiPk()
    {
        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('PatientData');
        $record = $dbMapper->find(array('id'=>1, 'patient__id'=>1));

        $this->assertEquals(1,$record->id);
        $this->assertEquals(1,$record->patient__id);
    }

    /**
     * FindMany test 1
     **/
    public function testFindManySimple()
    {
        $searchParams = array(
            'PatientData' => array('zip_code'=>'00-002')
        );

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('PatientData');
        $searchResult = $dbMapper->findMany($searchParams);

        if (count($searchResult) != 2){
            $this->assertEquals(2, count($searchResult));
        }
    }

    /**
     * FindMany test 2
     **/
    public function testFindManyComplex()
    {
        $searchParams = array(
            'PatientData' => array(
                'zip_code' => '00-002',
                'med__id' => 1,
            )
        );

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('PatientData');
        $searchResult = $dbMapper->findMany($searchParams);

        if (count($searchResult) != 1){
            $this->assertEquals(1, count($searchResult));
        }
    }



}
