<?php namespace Linchpinstudios\Theme\Commands;

use Illuminate\Console\Command;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem as File;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Linchpinstudios\Theme\Helpers\StubProcess;

class ThemeGeneratorCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'theme:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate theme structure';

    /**
     * Repository config.
     *
     * @var Illuminate\Config\Repository
     */
    protected $config;

    /**
     * Filesystem
     *
     * @var Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Filesystem
     *
     * @var Illuminate\Filesystem\Filesystem
     */
    protected $composer;

    /**
     * Working Directory
     *
     * @var Illuminate\Filesystem\Filesystem
     */
    protected $workingDir = '/../resources/stubs/theme';

    /**
     * Create a new command instance.
     *
     * @param \Illuminate\Config\Repository     $config
     * @param \Illuminate\Filesystem\Filesystem $files
     * @return \Linchpinstudios\Theme\Commands\ThemeGeneratorCommand
     */
    public function __construct(Repository $config, File $files)
    {
        $this->config = $config;

        $this->files = $files;

        $this->composer = app()['composer'];

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {

        $data = [
            'themeName' => $this->getTheme(),
            'data' => date(F d, Y G:i:s),
        ];

        $processor = new StubProcess( $this->getTheme(), $this->getPath(null) );

        // The theme is already exists.
        if ($this->files->isDirectory($this->getPath(null)))
        {
            return $this->error('Theme "'.$this->getTheme().'" is already exists.');
        }

        // Directories.
        $directory = $this->config->get('theme.containerDir');

        $process = $processor->getFiles( $this->workingDir );

        $this->info('Theme "'.$this->getTheme().'" has been created.');

    }


    /**
     * Make directory.
     *
     * @param  string $directory
     * @return void
     */
    protected function makeDir($directory)
    {
        if ( !$this->files->isDirectory($this->getPath($directory)) )
        {
            $this->files->makeDirectory($this->getPath($directory), 0777, true);
        }
    }

    /**
     * Make file.
     *
     * @param  string $file
     * @param  string $template
     * @return void
     */
    protected function makeFile($file, $template = null)
    {


        if ( ! $this->files->exists($this->getPath($file)))
        {
            $content = $this->getPath($file);

            $facade = $this->option('facade');
            if ( ! is_null($facade))
            {
                $template = preg_replace('/Theme(\.|::)/', $facade.'$1', $template);
            }

            $this->files->put($content, $template);
        }
    }

    /**
     * Get root writable path.
     *
     * @param  string $path
     * @return string
     */
    protected function getPath($path)
    {
        $rootPath = $this->option('path');

        return $rootPath.'/'.strtolower($this->getTheme()).'/' . $path;
    }

    /**
     * Get the theme name.
     *
     * @return string
     */
    protected function getTheme()
    {
        return strtolower( $this->argument('name') );
    }

    /**
     * Get default template.
     *
     * @param  string $template
     * @return string
     */
    protected function getTemplate($template)
    {
        $path = realpath(__DIR__.'/../templates/'.$template.'.txt');

        return $this->files->get($path);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'Name of the theme to generate.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        $path = base_path() . '/resources/' . $this->config->get('theme.themeDir');

        return array(
            array('path', null, InputOption::VALUE_OPTIONAL, 'Path to theme directory.', $path),
            array('facade', null, InputOption::VALUE_OPTIONAL, 'Facade name.', null),
        );
    }

}
