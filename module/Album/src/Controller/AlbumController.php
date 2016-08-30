<?php
namespace Album\Controller;

use Album\Model\AlbumTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Album\Form\AlbumForm;
use Album\Model\Album;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Zend\Paginator\Paginator;
 use Zend\Db\Sql\Select;
 use Album\Form\SearchForm;

class AlbumController extends AbstractActionController
{
	 private $table;
	 public function __construct(AlbumTable $table)
     {
		$this->table = $table;
	 }


    public function indexAction()
    {
	    $searchform = new SearchForm();
            $searchform->get('submit')->setValue('search'); 
            $select = new Select();
            $order_by = $this->params()->fromRoute('order_by') ?
            $this->params()->fromRoute('order_by') : 'id';
            $search="";
            $order = $this->params()->fromRoute('order') ?
                     $this->params()->fromRoute('order') : Select::ORDER_ASCENDING;
            $page = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;
            $request = $this->getRequest();
            if ($request->isGet()) {
                $formdata    = (array) $request->getQuery();
                $search_data = array();
                foreach ($formdata as $key => $value) {
                 if ($key != 'submit') {
                    if (!empty($value)) {
                        $search_data[$key] = $value;
                    }
                }
            }
            if (!empty($search_data)) {
                $search = $search_data;
            }
            $searchform->setData($formdata);
			             $search_by = $this->params()->fromQuery() ?
                $this->params()->fromQuery() : '';
           
            $paginator = $this->table->fetchAll($order_by,$order,$search,$select);
			$page = (int) $this->params()->fromQuery('page', 1);
			$page = ($page < 1) ? 1 : $page;
			$paginator->setCurrentPageNumber($page);
			$paginator->setItemCountPerPage(10);
			return new ViewModel([
					'search_by'=> $search_by,
                    'order_by' => $order_by,
                    'order' => $order,
                    'page' => $page,
                    'paginator' => $paginator,
                    'pageAction' => 'album',
                    'form'       => $searchform,
                    ]);
            }
    }
    
    public function addAction()
    {
		 $form = new AlbumForm();
         $form->get('submit')->setValue('Add');
		 $request = $this->getRequest();

        if (! $request->isPost()) {
            return ['form' => $form];
        }

        $album = new Album();
        $form->setInputFilter($album->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return ['form' => $form];
        }
        $album->exchangeArray($form->getData());
        $this->table->saveAlbum($album);
        return $this->redirect()->toRoute('album');
    }

    public function editAction()
    {
		$id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('album', ['action' => 'add']);
        }
		try {
            $album = $this->table->getAlbum($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('album', ['action' => 'index']);
        }

        $form = new AlbumForm();
        $form->bind($album);
        $form->get('submit')->setAttribute('value', 'Edit');
        
        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form];
           
        if (! $request->isPost()) {
            return $viewData;
        }
          
        $form->setInputFilter($album->getInputFilter());
        $form->setData($request->getPost());
      
        if (! $form->isValid()) {
            return $viewData;
        }

        $this->table->saveAlbum($album);
		return $this->redirect()->toRoute('album', ['action' => 'index']);
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('album');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->table->deleteAlbum($id);
            }
           return $this->redirect()->toRoute('album');
        }

        return [
            'id'    => $id,
            'album' => $this->table->getAlbum($id),
        ];
    }
}