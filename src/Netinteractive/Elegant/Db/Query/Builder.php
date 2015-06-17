<?php
namespace Netinteractive\Elegant\Db\Query;

use Illuminate\Database\ConnectionInterface AS ConnectionInterface;
use Illuminate\Database\Query\Grammars\Grammar AS Grammar;
use Illuminate\Database\Query\Processors\Processor AS Processor;
use Illuminate\Database\Query\Builder AS BaseBuilder;


/**
 * Class Builder
 * @package Netinteractive\Elegant\Db\Query
 */
class Builder extends BaseBuilder
{
    /**
     * Defines if additional query filters are avaible
     * @var bool
     */
    protected $allowQueryFilter = true;

    /**
     * List of QueryBuilder objects to build WITH statment
     * @var array
     */
    protected $with = array();

    /**
     * list of sql comments
     * @var array
     */
    protected $comments = array();

    /**
     * Create a new query builder instance.
     *
     * @param  \Illuminate\Database\ConnectionInterface $connection
     * @param  \Illuminate\Database\Query\Grammars\Grammar $grammar
     * @param  \Illuminate\Database\Query\Processors\Processor $processor
     */
    public function __construct(ConnectionInterface $connection = null, Grammar $grammar = null, Processor $processor = null)
    {
        $this->bindings['with'] = array();

        return parent::__construct($connection, $grammar, $processor);
    }


    /**
     * Get a new instance of the query builder.
     *
     * @return \Netinteractive\Elegant\Db\Query\Builder
     */
    public function newQuery()
    {
        return \App('ni.elegant.db.query.builder');
    }

    /**
     * Allows to select from table or other QueryBuilder object. We can provide an alias for query, so later we can
     * get this query or his params by alias.
     * @param string $from
     * @param string $alias
     * @return $this
     */
    public function from($from, $alias = null)
    {
        if ($from instanceof \Netinteractive\Elegant\Db\Query\Builder) {
            if (empty($alias)) {
                $alias = $from->getFrom();
            }

            $this->from = $this->getConnection()->raw(sprintf('(%s) as %s', $from->prepareQuery(), $alias));
            $this->mergeBindings($from);
        } else {
            $this->from = $from;
        }

        return $this;
    }

    /**
     * Binds QueryObject to $alias and later makes SQL WITH statement
     * @param \Netinteractive\Elegant\Db\Query\Builder $query
     * @param $alias
     * @return $this
     */
    public function addWith(Builder $query, $alias)
    {
        $this->with[$alias] = $query;
        $this->mergeBindings($query);
        return $this;
    }

    /**
     * Turns on/off query filters
     * @param bool $allow
     */
    public function allowFilter($allow = true)
    {
        $this->allowQueryFilter = $allow;
        return $this;
    }

    /**
     * Execute a query for a single record by ID.
     *
     * @param  array $ids
     * @param  array $columns
     * @return mixed|static
     */
    public function find($ids, $columns = array('*'))
    {
        if (is_array($ids)) {
            foreach ($ids AS $key => $val) {
                $this->where($key, '=', $val);
            }
        } else {
            $this->where('id', '=', $ids);
        }

        return $this->first($columns);
    }


    /**
     * Delete a record from the database.
     *
     * @param  mixed $ids
     * @return int
     */
    public function delete($ids = null)
    {
        // If an ID is passed to the method, we will set the where clause to check
        // the ID to allow developers to simply and quickly remove a single row
        // from their database without manually specifying the where clauses.
        if (!is_null($ids)) {
            if (is_array($ids)) {
                foreach ($ids AS $key => $val) {
                    $this->where($key, '=', $val);
                }
            } else {
                $this->where('id', '=', $ids);
            }
        }

        $sql = $this->grammar->compileDelete($this);

        return $this->connection->delete($sql, $this->getBindings());
    }


    /**
     * Execute the query and get the first result.
     *
     * @param  array $columns
     * @return \Netinteractive\Elegant\Model\Record|static|null
     */
    public function first($columns = array('*'))
    {
        return $this->take(1)->get($columns)->first();
    }

    /**
     * Returns table name
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Adds comment to query (will be added to sql)
     * @param string $comment
     * @return $this
     */
    public function addComment($comment)
    {
        $this->comments[] = $comment;
        return $this;
    }

    /**
     * Method allows to override already binded value
     *
     * @param string $type
     * @param string $alias
     * @param mixed $value
     * @return bool
     */
    public function setBinding($type, $alias, $value)
    {
        if (isSet($this->bindings[$type][$alias])) {
            $this->addComment("[Binding] [$alias] " . $this->bindings[$type][$alias] . ' => ' . $value);
            $this->bindings[$type][$alias] = $value;
            return true;
        }
        return false;
    }

    #
    # WHERES SECTION
    #

    /**
     * Allows to override query wheres
     * @param array $wheres
     * @return array
     * @return $this;
     */
    protected function setWheres(array $wheres)
    {
        $this->wheres = $wheres;
        return $this;
    }


    /**
     * Returns array of wheres
     * @return array
     */
    protected function getWheres()
    {
        return $this->wheres;
    }

    /**
     * Reset query wheres
     * @return $this
     */
    protected function clearWheres()
    {
        $this->wheres = compact('type', 'column', 'operator', 'value', 'boolean');
        return $this;
    }

    /**
     * Add a basic where clause to the query.
     *
     * @param  string $column
     * @param  string $operator
     * @param  mixed $value
     * @param  string $boolean
     * @param  string $alias
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and', $alias = null)
    {
        // If the column is an array, we will assume it is an array of key-value pairs
        // and can add them each as a where clause. We will maintain the boolean we
        // received when the method was called and pass it into the nested where.
        if (is_array($column)) {
            return $this->whereNested(function ($query) use ($column) {
                foreach ($column as $key => $value) {
                    $query->where($key, '=', $value);
                }
            }, $boolean, $alias);
        }

        // Here we will make some assumptions about the operator. If only 2 values are
        // passed to the method, we will assume that the operator is an equals sign
        // and keep going. Otherwise, we'll require the operator to be passed in.
        if (func_num_args() == 2) {
            list($value, $operator) = array($operator, '=');
        } elseif ($this->invalidOperatorAndValue($operator, $value)) {
            throw new \InvalidArgumentException(_("Value must be provided."));
        }

        // If the columns is actually a Closure instance, we will assume the developer
        // wants to begin a nested where statement which is wrapped in parenthesis.
        // We'll add that Closure to the query then return back out immediately.
        if ($column instanceof \Closure) {
            return $this->whereNested($column, $boolean, $alias);
        }

        // If the value is a Closure, it means the developer is performing an entire
        // sub-select within the query and we will need to compile the sub-select
        // within the where clause to get the appropriate query record results.
        if ($value instanceof \Closure) {
            return $this->whereSub($column, $operator, $value, $boolean, $alias);
        }

        // If the value is "null", we will just assume the developer wants to add a
        // where null clause to the query. So, we will allow a short-cut here to
        // that method for convenience so the developer doesn't have to check.
        if (is_null($value)) {
            return $this->whereNull($column, $boolean, $operator != '=', $alias);
        }

        // Now that we are working with just a simple query we can put the elements
        // in our array and add the query binding to our array of bindings that
        // will be bound to each SQL statements when it is finally executed.
        $type = 'Basic';

        if ($alias) {
            $this->wheres[$alias] = compact('type', 'column', 'operator', 'value', 'boolean');
        } else {
            $this->wheres[] = compact('type', 'column', 'operator', 'value', 'boolean');
        }


        if (!$value instanceof Expression) {
            $this->addBinding($value, 'where', $alias);
        }

        return $this;
    }

    /**
     * Add a binding to the query.
     *
     * @param  mixed $value
     * @param  string $type
     * @param  string $alias
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function addBinding($value, $type = 'where', $alias = null)
    {
        if (!array_key_exists($type, $this->bindings)) {
            throw new \InvalidArgumentException("Invalid binding type: {$type}.");
        }

        if (is_array($value)) {
            if ($alias) {
                $this->bindings[$type][$alias] = array_values(array_merge($this->bindings[$type][$alias], $value));
            } else {
                $this->bindings[$type] = array_values(array_merge($this->bindings[$type], $value));
            }

        } else {
            if ($alias) {
                $this->bindings[$type][$alias] = $value;
            } else {
                $this->bindings[$type][] = $value;
            }

        }

        return $this;
    }


    /**
     * Add a full sub-select to the query.
     *
     * @param  string $column
     * @param  string $operator
     * @param  \Closure $callback
     * @param  string $boolean
     * @param  string $alias
     * @return $this
     */
    protected function whereSub($column, $operator, Closure $callback, $boolean, $alias = null)
    {
        $type = 'Sub';

        $query = $this->newQuery();

        // Once we have the query instance we can simply execute it so it can add all
        // of the sub-select's conditions to itself, and then we can cache it off
        // in the array of where clauses for the "main" parent query instance.
        call_user_func($callback, $query);

        if ($alias) {
            $this->wheres[$alias] = compact('type', 'column', 'operator', 'query', 'boolean');
        } else {
            $this->wheres[] = compact('type', 'column', 'operator', 'query', 'boolean');
        }


        $this->mergeBindings($query);

        return $this;
    }


    /**
     * Add a nested where statement to the query.
     *
     * @param  \Closure $callback
     * @param  string $boolean
     * @param  string $aliast
     * @return \Netinteractive\Elegant\Db\Query\Builder|static
     */
    public function whereNested(\Closure $callback, $boolean = 'and', $alias = null)
    {
        // To handle nested queries we'll actually create a brand new query instance
        // and pass it off to the Closure that we have. The Closure can simply do
        // do whatever it wants to a query then we will store it for compiling.
        $query = $this->newQuery();

        $query->from($this->from);

        call_user_func($callback, $query);

        return $this->addNestedWhereQuery($query, $boolean, $alias);
    }

    /**
     * Add another query builder as a nested where to the query builder.
     *
     * @param  \Netinteractive\Elegant\Db\Query\Builder|static $query
     * @param  string $boolean
     * @return $this
     */
    public function addNestedWhereQuery($query, $boolean = 'and', $alias = null)
    {
        if (count($query->wheres)) {
            $type = 'Nested';

            if ($alias) {
                $this->wheres[$alias] = compact('type', 'query', 'boolean');
            } else {
                $this->wheres[] = compact('type', 'query', 'boolean');
            }

            $this->mergeBindings($query);
        }

        return $this;
    }

    /**
     * Add an "or where" clause to the query.
     *
     * @param  string $column
     * @param  string $operator
     * @param  mixed $value
     * @param  string $alias
     * @return \Netinteractive\Elegant\Db\Query\Builder|static
     */
    public function orWhere($column, $operator = null, $value = null, $alias = null)
    {
        return $this->where($column, $operator, $value, 'or', $alias);
    }

    /**
     * Add a raw where clause to the query.
     *
     * @param  string $sql
     * @param  array $bindings
     * @param  string $boolean
     * @param  string $alias
     * @return $this
     */
    public function whereRaw($sql, array $bindings = array(), $boolean = 'and', $alias = null)
    {
        $type = 'raw';

        if ($alias) {
            $this->wheres[$alias] = compact('type', 'sql', 'boolean');
        } else {
            $this->wheres[] = compact('type', 'sql', 'boolean');
        }


        $this->addBinding($bindings, 'where');

        return $this;
    }

    /**
     * Add a raw or where clause to the query.
     *
     * @param  string $sql
     * @param  array $bindings
     * @return \Netinteractive\Elegant\Db\Query\Builder|static
     */
    public function orWhereRaw($sql, array $bindings = array(), $alias = null)
    {
        return $this->whereRaw($sql, $bindings, 'or', $alias);
    }

    /**
     * Add a where between statement to the query.
     *
     * @param  string $column
     * @param  array $values
     * @param  string $boolean
     * @param  bool $not
     * @param  string $alias
     * @return $this
     */
    public function whereBetween($column, array $values, $boolean = 'and', $not = false, $alias = null)
    {
        $type = 'between';

        if ($alias) {
            $this->wheres[$alias] = compact('column', 'type', 'boolean', 'not');
        } else {
            $this->wheres[] = compact('column', 'type', 'boolean', 'not');
        }


        $this->addBinding($values, 'where', $alias);

        return $this;
    }

    /**
     * Add an or where between statement to the query.
     *
     * @param  string $column
     * @param  array $values
     * @param  string $alias
     * @return \Netinteractive\Elegant\Db\Query\Builder|static
     */
    public function orWhereBetween($column, array $values, $alias = null)
    {
        return $this->whereBetween($column, $values, 'or', $alias);
    }

    /**
     * Add a where not between statement to the query.
     *
     * @param  string $column
     * @param  array $values
     * @param  string $boolean
     * @param  string $alias
     * @return \Netinteractive\Elegant\Db\Query\Builder|static
     */
    public function whereNotBetween($column, array $values, $boolean = 'and', $alias = null)
    {
        return $this->whereBetween($column, $values, $boolean, true, $alias);
    }

    /**
     * Add an or where not between statement to the query.
     *
     * @param  string $column
     * @param  array $values
     * @param  string $alias
     * @return \Netinteractive\Elegant\Db\Query\Builder|static
     */
    public function orWhereNotBetween($column, array $values, $alias = null)
    {
        return $this->whereNotBetween($column, $values, 'or', $alias);
    }

    /**
     * Add an exists clause to the query.
     *
     * @param  \Closure $callback
     * @param  string $boolean
     * @param  bool $not
     * @param  string $alias
     * @return $this
     */
    public function whereExists(\Closure $callback, $boolean = 'and', $not = false, $alias = null)
    {
        $type = $not ? 'NotExists' : 'Exists';

        $query = $this->newQuery();

        // Similar to the sub-select clause, we will create a new query instance so
        // the developer may cleanly specify the entire exists query and we will
        // compile the whole thing in the grammar and insert it into the SQL.
        call_user_func($callback, $query);

        if ($alias) {
            $this->wheres[$alias] = compact('type', 'operator', 'query', 'boolean');
        } else {
            $this->wheres[] = compact('type', 'operator', 'query', 'boolean');
        }


        $this->mergeBindings($query);

        return $this;
    }

    /**
     * Add an or exists clause to the query.
     *
     * @param  \Closure $callback
     * @param  bool $not
     * @param  string $alias
     * @return \Netinteractive\Elegant\Db\Query\Builder|static
     */
    public function orWhereExists(\Closure $callback, $not = false, $alias = null)
    {
        return $this->whereExists($callback, 'or', $not, $alias);
    }

    /**
     * Add a where not exists clause to the query.
     *
     * @param  \Closure $callback
     * @param  string $boolean
     * @param  string $alias
     * @return \Netinteractive\Elegant\Db\Query\Builder|static
     */
    public function whereNotExists(\Closure $callback, $boolean = 'and', $alias = null)
    {
        return $this->whereExists($callback, $boolean, true, $alias);
    }

    /**
     * Add a where not exists clause to the query.
     *
     * @param  \Closure $callback
     * @param  string $alias
     * @return \Netinteractive\Elegant\Db\Query\Builder|static
     */
    public function orWhereNotExists(\Closure $callback, $alias = null)
    {
        return $this->orWhereExists($callback, true, $alias);
    }

    /**
     * Add a "where in" clause to the query.
     *
     * @param  string $column
     * @param  mixed $values
     * @param  string $boolean
     * @param  bool $not
     * @param  string $alias
     * @return $this
     */
    public function whereIn($column, $values, $boolean = 'and', $not = false, $alias = null)
    {
        $type = $not ? 'NotIn' : 'In';

        // If the value of the where in clause is actually a Closure, we will assume that
        // the developer is using a full sub-select for this "in" statement, and will
        // execute those Closures, then we can re-construct the entire sub-selects.
        if ($values instanceof \Closure) {
            return $this->whereInSub($column, $values, $boolean, $not);
        }

        if (!is_array($values)){
            $values = array($values);
        }

        if ($alias) {
            $this->wheres[$alias] = compact('type', 'column', 'values', 'boolean');
        } else {
            $this->wheres[] = compact('type', 'column', 'values', 'boolean');
        }


        $this->addBinding($values, 'where');

        return $this;
    }

    /**
     * Add an "or where in" clause to the query.
     *
     * @param  string $column
     * @param  mixed $values
     * @return \Netinteractive\Elegant\Db\Query\Builder|static
     */
    public function orWhereIn($column, $values, $alias = null)
    {
        return $this->whereIn($column, $values, 'or', $alias);
    }

    /**
     * Add a "where not in" clause to the query.
     *
     * @param  string $column
     * @param  mixed $values
     * @param  string $boolean
     * @return \Netinteractive\Elegant\Db\Query\Builder|static
     */
    public function whereNotIn($column, $values, $boolean = 'and', $alias = null)
    {
        return $this->whereIn($column, $values, $boolean, true, $alias);
    }

    /**
     * Add an "or where not in" clause to the query.
     *
     * @param  string $column
     * @param  mixed $values
     * @return \Netinteractive\Elegant\Db\Query\Builder|static
     */
    public function orWhereNotIn($column, $values, $alias = null)
    {
        return $this->whereNotIn($column, $values, 'or', $alias);
    }

    /**
     * Add a where in with a sub-select to the query.
     *
     * @param  string $column
     * @param  \Closure $callback
     * @param  string $boolean
     * @param  bool $not
     * @return $this
     */
    protected function whereInSub($column, \Closure $callback, $boolean, $not, $alias = null)
    {
        $type = $not ? 'NotInSub' : 'InSub';

        // To create the exists sub-select, we will actually create a query and call the
        // provided callback with the query so the developer may set any of the query
        // conditions they want for the in clause, then we'll put it in this array.
        call_user_func($callback, $query = $this->newQuery());

        if ($alias) {
            $this->wheres[$alias] = compact('type', 'column', 'query', 'boolean');
        } else {
            $this->wheres[] = compact('type', 'column', 'query', 'boolean');
        }


        $this->mergeBindings($query);

        return $this;
    }

    /**
     * Add a "where null" clause to the query.
     *
     * @param  string $column
     * @param  string $boolean
     * @param  bool $not
     * @return $this
     */
    public function whereNull($column, $boolean = 'and', $not = false, $alias = null)
    {
        $type = $not ? 'NotNull' : 'Null';

        if ($alias) {
            $this->wheres[$alias] = compact('type', 'column', 'boolean');
        } else {
            $this->wheres[] = compact('type', 'column', 'boolean');
        }


        return $this;
    }

    /**
     * Add an "or where null" clause to the query.
     *
     * @param  string $column
     * @return \Netinteractive\Elegant\Db\Query\Builder|static
     */
    public function orWhereNull($column, $alias = null)
    {
        return $this->whereNull($column, 'or', $alias);
    }

    /**
     * Add a "where not null" clause to the query.
     *
     * @param  string $column
     * @param  string $boolean
     * @return \Netinteractive\Elegant\Db\Query\Builder|static
     */
    public function whereNotNull($column, $boolean = 'and', $alias = null)
    {
        return $this->whereNull($column, $boolean, true, $alias);
    }

    /**
     * Add an "or where not null" clause to the query.
     *
     * @param  string $column
     * @return \Netinteractive\Elegant\Db\Query\Builder|static
     */
    public function orWhereNotNull($column, $alias = null)
    {
        return $this->whereNotNull($column, 'or', $alias);
    }

    /**
     * Add a "where date" statement to the query.
     *
     * @param  string $column
     * @param  string $operator
     * @param  int $value
     * @param  string $boolean
     * @return \Netinteractive\Elegant\Db\Query\Builder|static
     */
    public function whereDate($column, $operator, $value, $boolean = 'and', $alias = null)
    {
        return $this->addDateBasedWhere('Date', $column, $operator, $value, $boolean, $alias);
    }

    /**
     * Add a "where day" statement to the query.
     *
     * @param  string $column
     * @param  string $operator
     * @param  int $value
     * @param  string $boolean
     * @return \Netinteractive\Elegant\Db\Query\Builder|static
     */
    public function whereDay($column, $operator, $value, $boolean = 'and', $alias = null)
    {
        return $this->addDateBasedWhere('Day', $column, $operator, $value, $boolean, $alias);
    }

    /**
     * Add a "where month" statement to the query.
     *
     * @param  string $column
     * @param  string $operator
     * @param  int $value
     * @param  string $boolean
     * @return \Netinteractive\Elegant\Db\Query\Builder|static
     */
    public function whereMonth($column, $operator, $value, $boolean = 'and', $alias = null)
    {
        return $this->addDateBasedWhere('Month', $column, $operator, $value, $boolean, $alias);
    }

    /**
     * Add a "where year" statement to the query.
     *
     * @param  string $column
     * @param  string $operator
     * @param  int $value
     * @param  string $boolean
     * @return \Netinteractive\Elegant\Db\Query\Builder|static
     */
    public function whereYear($column, $operator, $value, $boolean = 'and', $alias = null)
    {
        return $this->addDateBasedWhere('Year', $column, $operator, $value, $boolean, $alias);
    }

    /**
     * Add a date based (year, month, day) statement to the query.
     *
     * @param  string $type
     * @param  string $column
     * @param  string $operator
     * @param  int $value
     * @param  string $boolean
     * @return $this
     */
    protected function addDateBasedWhere($type, $column, $operator, $value, $boolean = 'and', $alias = null)
    {
        if ($alias) {
            $this->wheres[$alias] = compact('column', 'type', 'boolean', 'operator', 'value');
        } else {
            $this->wheres[] = compact('column', 'type', 'boolean', 'operator', 'value');
        }


        $this->addBinding($value, 'where');

        return $this;
    }

    /**
     * @return string
     */
    protected function prepareQuery()
    {

        #We have to wrap wheres with other where statement, so query filter mechanism won't broke our original query
        $wheres = $this->getWheres();
        $this->clearWheres();


        $this->where(function ($query) use ($wheres) {
            if (!empty($wheres)) {

                $query->setWheres($wheres);
            }

            return $query;
        });

        #Here we check if additional Query Filters are avaible. If so, we fire the Event
        if ($this->allowQueryFilter == true) {
            \Event::fire('query.filter.role', array($this), false);
        }

        #Here we add comments to Sql
        $comments = '';
        if (count($this->comments)) {
            $comments .= "/*\n";
            foreach ($this->comments AS $comment) {
                $comments .= "*  " . $comment . "\n";
            }
            $comments .= "*/\n";
        }

        #Here we build WITH statment (PostgreSQL)
        $with = '';
        $max = count($this->with);

        if ($max) {
            $with .= 'WITH ';
            $i = 1;

            foreach ($this->with as $alias => $withQuery) {
                $with .= "$alias AS (";
                $with .= $this->grammar->compileSelect($withQuery);
                $with .= ')';

                if ($i < $max) {
                    $with .= ',';
                }
                $i++;
            }
        }

        $this->removeDoubleJoins();

        return $comments . $with . $this->grammar->compileSelect($this);
    }

    /**
     * Get the SQL representation of the query.
     *
     * @return string
     */
    public function toSql()
    {
        return $this->prepareQuery();
    }

    /**
     * Remove order by from query
     */
    public function removeOrder()
    {
        $this->orders = null;
        $this->bindings['order'] = [];
    }

    /**
     * Method checks if we have any double joins and removes doubled ones
     * @return void
     */
    protected function removeDoubleJoins()
    {
        if (count($this->joins) == 0) {
            return;
        }

        $joins = array();
        foreach ($this->joins AS $i => $val) {
            $key = md5($this->joins[$i]->table . '_' . http_build_query($this->joins[$i]->clauses));
            if (!isSet($joins[$key])) {
                $joins[$key] = $i;
            } else {
                unset($this->joins[$joins[$key]]);
                $joins[$key] = $i;
            }
        }
    }
}