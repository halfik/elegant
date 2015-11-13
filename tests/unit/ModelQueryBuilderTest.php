<?php


class ModelQueryBuilderTest extends ElegantTest
{
    /**
     * Get with soft deleted record test 1
     */
    public function testGetSoftDelete()
    {
        DB::beginTransaction();

        $dbMapper = new \Netinteractive\Elegant\Mapper\DbMapper('Med');
        $record = $dbMapper->find(1);

        $dbMapper->delete( $record );

        $results = $dbMapper->get();

        $this->assertEquals(1, count($results));

        DB::rollback();
    }

}