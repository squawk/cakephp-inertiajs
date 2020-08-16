<?php
namespace Inertia\View;

use Cake\Routing\Router;

trait BaseViewTrait
{
    /**
     * Get current absolute url.
     *
     * @return string
     */
    private function getCurrentUri()
    {
        return Router::url($this->getRequest()->getRequestTarget(), true);
    }

    /**
     * Returns component name.
     * If passed via contoller using `component` key, will use that.
     * Otherwise, will return the combination of controller and action.
     *
     * That means, it will generate `Users/Index` component for `UsersController.php`'s `index` action.
     *
     * @return string
     */
    private function getComponentName()
    {
        if ($this->get('component') !== null) {
            return $this->get('component');
        }

        return sprintf(
            '%s/%s',
            $this->getRequest()->getParam('controller'),
            ucwords($this->getRequest()->getParam('action'))
        );
    }

    /**
     * Returns `props` array excluding the default variables.
     *
     * @return array
     */
    private function getProps()
    {
        $props = [];

        foreach ($this->viewVars as $varName => &$value) {
            if (in_array($varName, ['component', 'url', 'props'])) {
                continue;
            }

            $props[$varName] = $value;

            unset($this->viewVars[$varName]);
        }

        return $props;
    }
}
