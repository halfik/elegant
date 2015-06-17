# Relations

We took all code from Eloquent and adapt it to our needs.

List of avaible relation types:

* BelongsTo
* BelongsToMany
* HasMany
* HasOne

Morphic relation types are not implemented yet, but we are going to do so in future.
HasManyThrough is not here and won't be (it never work for us).

Information about related data are defined in blueprint. To do so use RelationManger.
RelationManager is responsible for knowing how relations are defined and keeping informations about translators that knows to use this informations.

RelationManger is used by mappers when you want them to load some data with related records. Not all mappers will handle related data types.


## Examples

### Example 1

    use Netinteractive\Elegant\Model\Blueprint AS BaseBluePrint;
    use Netinteractive\Elegant\Search\Searchable;

    class Blueprint extends BaseBluePrint
    {
       protected function init()
        {
            $this->setStorageName('patient');
            $this->primaryKey = array('id');
            $this->incrementingPk = 'id';

            $this->getRelationManager()->hasMany('patientData','PatientData', array('patient__id'), array('id') );
            $this->getRelationManager()->belongsTo('user','User', array('user__id'), array('id') );

            return parent::init();
        }
    }