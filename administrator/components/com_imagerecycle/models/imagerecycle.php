<?php
/**
 * Droptables
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@imagerecycle.com *
 * @package Droptables
 * @copyright Copyright (C) 2014 ImageRecycle (http://www.imagerecycle.com). All rights reserved.
 * @copyright Copyright (C) 2014 Damien Barrère (http://www.crac-design.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */
 
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

class ImagerecycleModelImagerecycle extends JModelList
{

    private $allowed_ext = array('jpg', 'jpeg', 'png', 'gif', 'pdf');
    private $allowedPath = array('images', 'media', 'templates');
    protected $totalImages = 0;
    protected $totalFile = 0;
    protected $time_elapsed_secs = 0;
    protected $totalOptimizedImages = 0;

    protected $order_by = '';
    protected $order_dir = 'asc';

    /**
     * Constructor.
     *
     * @param   array $config An optional associative array of configuration settings.
     *
     * @see     JController
     * @since   1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'filename', 'a.filename',
                'filesize', 'a.filesize',
                'status', 'a.status',
                'date', 'a.date',
            );
        }
        parent::__construct($config);

    }

    public function getItems()
    {
        $optimized = $this->getUserStateFromRequest($this->context . '.filter.optimized', 'filter_optimized');
        $filters = array('optimized' => $optimized);
        $images = $this->_getLocalImages($filters);

        $images = $this->prepareLocalImages($images);
        return $images;
    }

    public function getTotal()
    {
        return $this->totalImages;
    }

    public function prepareLocalImages($images)
    {
        //process data before display                   
        $input = JFactory::getApplication()->input;
        $filter_fields = array('a.filename', 'a.filesize', 'a.status', 'a.date');

        $order_by = $this->getState('list.ordering');
        $dir = $this->getState('list.direction');

        if (in_array($order_by, $filter_fields)) {
            if ($dir == 'desc') {
                $this->order_dir = 'desc';
            } else {
                $this->order_dir = 'asc';
            }
        } else {
            $order_by = '';//default - image path
        }
        $this->order_by = $order_by;
        if ($order_by == 'a.filesize') {
            usort($images, array("ImagerecycleModelImagerecycle", "cmpSize"));
        } else if ($order_by == 'a.status') {
            usort($images, array("ImagerecycleModelImagerecycle", "cmpStatus"));
        } else if ($order_by == 'a.filename' && $this->order_dir == 'desc') {
            usort($images, array("ImagerecycleModelImagerecycle", "cmpNameDesc"));
        } else if ($order_by == 'a.filename') {
            usort($images, array("ImagerecycleModelImagerecycle", "cmpName"));
        } else if ($order_by == 'a.date' && $this->order_dir == 'desc') {
            usort($images, array("ImagerecycleModelImagerecycle", "cmpDateDesc"));            
        }else if ($order_by == 'a.date') {
            usort($images, array("ImagerecycleModelImagerecycle", "cmpDate"));            
        }

        $pagination = $this->getPagination();
        $result = array_slice($images, $pagination->limitstart, $pagination->limit);

        return $result;
    }

    public function _getLocalImages($filters = array())
    {

        $optimizedFiles = array();
        $db = JFactory::getDbo();
        $sql = "SELECT file,size_before,status,expiration_date FROM #__imagerecycle_files ";
        $db->setQuery($sql);
        $optimizedFiles = $db->loadAssocList('file');

        if (isset($filters['optimized']) && $filters['optimized']) {
            $optimizedOnly = ($filters['optimized'] == 'yes') ? true : false;
            $filter_optimized = true;
        } else {
            $filter_optimized = false;
        }

        $componentParams = JComponentHelper::getParams('com_imagerecycle');
        $min_size = (int)$componentParams->get('min_size', 1) * 1024;
        $max_size = (int)$componentParams->get('max_size', 5 * 1024) * 1024;
        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');

        $images = array();
        $base_dir = JPATH_ROOT;
        $start = microtime(true);
        clearstatcache();
        $counter = 0;
        $now = time();
        $include_folders = $componentParams->get('include_folders', '');
        if (!empty($include_folders)) {
            if (is_array($include_folders)) {
                $this->allowedPath = $include_folders;
            } else {
                $this->allowedPath = explode(',', $include_folders);
            }

        }

        $allowed_ext = array();
        foreach ($this->allowed_ext as $ext) {
            $compression_type = $componentParams->get('compression_type_' . $ext, '');
            if ($compression_type !== "none") {
                $allowed_ext[] = $ext;
            }
        }
        $this->allowed_ext = $allowed_ext;

        include_once(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_imagerecycle' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'filesystem.php');
        foreach ($this->allowedPath as $cur_dir) {
            $scan_dir = JPATH_ROOT . DIRECTORY_SEPARATOR . $cur_dir;
            $files = ImageRecycle\Standalone\Filesystem::listFiles($scan_dir, $this->allowed_ext, IR_ITERATOR_ONLYFILE);
            foreach ($files as $filename) {
                $filename = $scan_dir . $filename;
                if (!is_file($filename)) continue;
                $this->totalFile++;

                $data = array();                
                $data['filename'] = str_replace('\\', '/', substr($filename, strlen($base_dir) + 1));

                $data['date'] = filectime($filename);                
                $data['size'] = filesize($filename);
                $data['filetype'] = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                if (!in_array($data['filetype'], $this->allowed_ext)) {
                    continue;
                }

                if (($data['size'] < $min_size && !isset($optimizedFiles[$data['filename']])) || $data['size'] > $max_size) {
                    continue;
                }
                if (!empty($search)) {
                    if (strpos($filename, $search) === FALSE) continue;
                }

                if (isset($optimizedFiles[$data['filename']]) && $optimizedFiles[$data['filename']]['status'] != "R") {
                    $data['optimized'] = true;
                    $this->totalOptimizedImages++;
                    $expirationTime = strtotime($optimizedFiles[$data['filename']]['expiration_date']);
                    if ($expirationTime < $now) {
                        $data['optimized_datas'] = array('expired' => true, 'size_before' => $optimizedFiles[$data['filename']]['size_before']);
                    } else {
                        $data['optimized_datas'] = $optimizedFiles[$data['filename']];
                    }
                } else if (isset($optimizedFiles[$data['filename']])) {
                    $data['optimized'] = false;
                    $data['optimized_datas'] = array('status' => "R");
                } else {
                    $data['optimized'] = false;
                    $data['optimized_datas'] = array('status' => "");
                }

                if ($filter_optimized && ($optimizedOnly !== $data['optimized'])) {
                    continue;
                }

                $this->totalImages++;
                $counter++;
                $images[] = $data;
            }
        }
        $this->time_elapsed_secs = microtime(true) - $start;

        return $images;
    }

    public function getTotalImages()
    {
        return $this->totalImages;
    }

    public function getTotalOptimizedImages()
    {
        return $this->totalOptimizedImages;
    }

    private function cmpStatus($a, $b)
    {
        if ($a['optimized'] == $b['optimized']) {
            return strcmp($a['filename'], $b['filename']);
        }

        if ($this->order_dir == 'asc') {
            return strcmp($a['optimized'], $b['optimized']);
        } else {
            return strcmp($b['optimized'], $a['optimized']);
        }
    }

    private function cmpSize($a, $b)
    {
        if ($a['size'] == $b['size']) {
            return strcmp($a['filename'], $b['filename']);
        }
        if ($this->order_dir == 'asc') {
            return ($a['size'] < $b['size']) ? -1 : 1;
        } else {
            return ($a['size'] < $b['size']) ? 1 : -1;
        }
    }

    private function cmpName($a, $b)
    {
        return strcmp($a['filename'], $b['filename']);
    }

    private function cmpNameDesc($a, $b)
    {
        return strcmp($b['filename'], $a['filename']);
    }

    private function cmpDate($a, $b)
    {
        return strcmp($a['date'], $b['date']);
    }

    private function cmpDateDesc($a, $b)
    {
        return strcmp($b['date'], $a['date']);
    }
    /**
     * Method to get a JPagination object for the data set.
     *
     * @return  JPagination  A JPagination object for the data set.
     *
     * @since   12.2
     */
    public function getPagination()
    {
        // Get a storage key.
        $store = $this->getStoreId('getPagination');

        // Try to load the data from internal storage.
        if (isset($this->cache[$store])) {
            return $this->cache[$store];
        }


        //Register  class
        JLoader::register('IRPagination', JPATH_ADMINISTRATOR . '/components/com_imagerecycle/helpers/pagination.php');

        // Create the pagination object.
        $limit = (int)$this->getState('list.limit') - (int)$this->getState('list.links');
        $page = new IRPagination($this->getTotal(), $this->getStart(), $limit);

        // Add the object to the internal cache.
        $this->cache[$store] = $page;

        return $this->cache[$store];
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param   string $ordering An optional ordering field.
     * @param   string $direction An optional direction (asc|desc).
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function populateState($ordering = null, $direction = null)
    {
        $app = JFactory::getApplication();

        // Adjust the context to support modal layouts.
        if ($layout = $app->input->get('layout')) {
            $this->context .= '.' . $layout;
        }

        $search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $optimized = $this->getUserStateFromRequest($this->context . '.filter.optimized', 'filter_optimized');
        $this->setState('filter.optimized', $optimized);

        if (empty($ordering)) {
            $ordering = $app->input->get('filter_order', 'a.filename');
            $direction = $app->input->get('filter_order_Dir', 'asc');
            $ordering = $this->getUserStateFromRequest($this->context . '.list.ordering', 'filter_order', $ordering);
            $direction = $this->getUserStateFromRequest($this->context . '.list.direction', 'filter_order_Dir', $direction);

            $this->setState('list.ordering', $ordering);
            $this->setState('list.direction', $direction);
        }

        $limit = $this->getUserStateFromRequest($this->context . '.list.limit', 'limit', $app->get('list_limit'));
        $this->setState('list.limit', $limit);

        // List state information.
        parent::populateState($ordering, $direction);
    }

    /**
     * Returns a Table object, always creating it.
     *
     * @param    type    The table type to instantiate
     * @param    string    A prefix for the table class name. Optional.
     * @param    array    Configuration array for model. Optional.
     *
     * @return    JTable    A database object
     */
    public function getTable($type = 'Images', $prefix = 'ImagerecycleTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true)
    {
        $form = $this->loadForm('com_imagerecycle.image', 'image', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        return $form;
    }


    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = $this->getItem();
        return $data;
    }

}