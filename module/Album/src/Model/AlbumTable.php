<?php
namespace Album\Model;
use RuntimeException;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Predicate\Like;

class AlbumTable
{
    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

     public function fetchAll($order_by, $order, $search_by, Select $select = null)
     {
         if (null === $select)
            $select = new Select();
        $select = new Select($this->tableGateway->getTable());
            $where    = new Where();
            $formdata=array();
            if (!empty($search_by))
            {
                $formdata = $search_by;
                if (!empty($formdata['search'])) 
                {
                    $where->addPredicate(new Like('artist','%' .$formdata['search'] . '%'))->orPredicate(new Like('title', '%' . $formdata['search'] . '%'));
                }
          
            }
            if (!empty($where)) {
                $select->where($where);
            }
            
            $select->order($order_by . ' ' . $order);
            
      /*  $resultSet = $this->tableGateway->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
    */
	    $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Album());
        $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter(),$resultSetPrototype);
        $paginator = new Paginator($paginatorAdapter);
        return $paginator;
    
	
	}
	
	public function getAlbum($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();
        if (! $row) {
            throw new RuntimeException(sprintf(
                'Could not find row with identifier %d',
                $id
            ));
        }

        return $row;
    }

    public function saveAlbum(Album $album)
    {
        $data = [
            'artist' => $album->artist,
            'title'  => $album->title,
        ];

        $id = (int) $album->id;

        if ($id === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        if (! $this->getAlbum($id)) {
            throw new RuntimeException(sprintf(
                'Cannot update album with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function deleteAlbum($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }
}