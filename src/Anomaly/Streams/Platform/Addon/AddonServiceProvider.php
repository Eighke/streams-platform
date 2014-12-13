<?php namespace Anomaly\Streams\Platform\Addon;

use Anomaly\Streams\Platform\Addon\Event\AddonsHaveRegistered;
use Composer\Autoload\ClassLoader;
use Laracasts\Commander\Events\DispatchableTrait;
use Laracasts\Commander\Events\EventGenerator;

class AddonServiceProvider extends \Illuminate\Support\ServiceProvider
{
    use EventGenerator;
    use DispatchableTrait;

    /**
     * Defer loading this service provider.
     *
     * @var bool
     */
    protected $defer = true;

    public function register()
    {
        $this->app->instance('streams.addon.paths', new AddonPaths());
        $this->app->instance('streams.addon.binder', new AddonBinder());
        $this->app->instance('streams.addon.loader', new AddonLoader());
        $this->app->instance('streams.addon.vendor', new AddonVendor());
        $this->app->instance('streams.addon.manager', new AddonManager());

        $this->registerListeners();

        $this->app->register('Anomaly\Streams\Platform\Addon\Distribution\DistributionServiceProvider');
        $this->app->register('Anomaly\Streams\Platform\Addon\Extension\ExtensionServiceProvider');
        $this->app->register('Anomaly\Streams\Platform\Addon\FieldType\FieldTypeServiceProvider');
        $this->app->register('Anomaly\Streams\Platform\Addon\Block\BlockServiceProvider');
        $this->app->register('Anomaly\Streams\Platform\Addon\Module\ModuleServiceProvider');
        $this->app->register('Anomaly\Streams\Platform\Addon\Theme\ThemeServiceProvider');
        $this->app->register('Anomaly\Streams\Platform\Addon\Tag\TagServiceProvider');

        $this->raise(new AddonsHaveRegistered());

        $this->dispatchEventsFor($this);
    }

    protected function registerListeners()
    {
        $this->app->make('events')->listen(
            'Anomaly.Streams.Platform.Application.Event.*',
            '\Anomaly\Streams\Platform\Addon\AddonListener'
        );
        $this->app->make('events')->listen(
            'Anomaly.Streams.Platform.Addon.Event.*',
            '\Anomaly\Streams\Platform\Addon\AddonListener'
        );
    }
}
