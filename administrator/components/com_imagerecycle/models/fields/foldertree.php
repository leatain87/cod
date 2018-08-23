<?php

/**
 * ImageRecycle
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@joomunited.com *
 * @package ImageRecycle
 * @copyright Copyright (C) 2014 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.folder');


/**
 * Supports an HTML select list of folder
 *
 * @since  11.1
 */
class JFormFieldFolderTree extends JFormField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  11.1
     */
    protected $type = 'FolderTree';

    /**
     * The filter.
     *
     * @var    string
     * @since  3.2
     */
    protected $filter;

    /**
     * The exclude.
     *
     * @var    string
     * @since  3.2
     */
    protected $exclude;

    /**
     * The hideNone.
     *
     * @var    boolean
     * @since  3.2
     */
    protected $hideNone = false;

    /**
     * The hideDefault.
     *
     * @var    boolean
     * @since  3.2
     */
    protected $hideDefault = false;

    /**
     * The directory.
     *
     * @var    string
     * @since  3.2
     */
    protected $directory;

    /**
     * Method to get certain otherwise inaccessible properties from the form field object.
     *
     * @param   string $name The property name for which to the the value.
     *
     * @return  mixed  The property value or null.
     *
     * @since   3.2
     */
    public function __get($name)
    {
        switch ($name) {
            case 'filter':
            case 'exclude':
            case 'hideNone':
            case 'hideDefault':
            case 'directory':
                return $this->$name;
        }

        return parent::__get($name);
    }

    /**
     * Method to get the user field input markup.
     *
     * @return  string  The field input markup.
     *
     * @since   1.6
     */
    protected function getInput()
    {
        $html = array();
        $groups = $this->getGroups();
        $excluded = $this->getExcluded();
        $link = 'index.php?option=com_imagerecycle&amp;view=folders&amp;layout=modal&amp;tmpl=component&amp;field=' . $this->id
            . (isset($groups) ? ('&amp;groups=' . base64_encode(json_encode($groups))) : '')
            . (isset($excluded) ? ('&amp;excluded=' . base64_encode(json_encode($excluded))) : '');

        // Initialize some field attributes.
        $attr = !empty($this->class) ? ' class="' . $this->class . '"' : '';
        $attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
        $attr .= $this->required ? ' required' : '';

        // Load the modal behavior script.
        JHtml::_('behavior.modal', 'a.modal_' . $this->id);

        // Build the script.
        $script = array();
        $script[] = '	function jSelectFolders_' . $this->id . '(id, title) {';
        $script[] = '		var old_id = document.getElementById("' . $this->id . '_id").value;';
        $script[] = '		if (old_id != id) {';
        $script[] = '			document.getElementById("' . $this->id . '_id").value = id;';
        $script[] = '			document.getElementById("' . $this->id . '").value = title;';
        $script[] = '			document.getElementById("'
            . $this->id . '").className = document.getElementById("' . $this->id . '").className.replace(" invalid" , "");';
        $script[] = '			' . $this->onchange;
        $script[] = '		}';
        $script[] = '		jModalClose();';
        $script[] = '	}';

        // Add the script to the document head.
        JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

        if (!empty($this->value)) {
            $value = $this->value;
        } else {
            $value = JText::_('COM_IMAGERECYCLE_CONFIG_SELECT_FOLDERS');
        }

        // Create a dummy text field with the user name.
        $html[] = '<div class="input-append">';
        $html[] = '	<input type="text" id="' . $this->id . '" value="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '"'
            . ' readonly' . $attr . ' />';

        // Create the user select button.
        if ($this->readonly === false) {
            $html[] = '		<a class="btn btn-primary modal_' . $this->id . '" title="' . JText::_('COM_IMAGERECYCLE_CONFIG_SELECT_FOLDERS') . '" href="' . $link . '"'
                . ' rel="{handler: \'iframe\', size: {x: 600, y: 400}}">';
            $html[] = '<i class="icon-folder"></i></a>';
        }

        $html[] = '</div>';

        // Create the real field, hidden, that stored the user id.
        $html[] = '<input type="hidden" id="' . $this->id . '_id" name="' . $this->name . '" value="' . $this->value . '" />';

        return implode("\n", $html);
    }


    /**
     * Method to get the filtering groups (null means no filtering)
     *
     * @return  mixed  array of filtering groups or null.
     *
     * @since   1.6
     */
    protected function getGroups()
    {
        return null;
    }

    /**
     * Method to get the users to exclude from the list of users
     *
     * @return  mixed  Array of users to exclude or null to to not exclude them
     *
     * @since   1.6
     */
    protected function getExcluded()
    {
        return null;
    }

    public function listFolders($dir, &$results)
    {
        $rootLen = strlen(JPATH_ROOT);
        if (!is_array($results)) $results = array();
        foreach (new DirectoryIterator($dir) as $fileInfo) {
            if (!$fileInfo->isDot()) {
                if ($fileInfo->isDir()) {
                    $pathName = $fileInfo->getPathname();
                    $folder = substr($pathName, $rootLen); //new stdClass();
                    //$folder->value = substr($pathName,$rootLen);
                    // $folder->text = substr($pathName,$rootLen);
                    $results[] = $folder;
                    $this->listFolders($pathName, $results);
                }
            }
        }

    }

}

