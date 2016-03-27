<?php namespace Linchpinstudios\Theme\Helpers;

use Illuminate\Filesystem\Filesystem as File;

class StubProcess {

    public $files;

    public $data;

    public $destination;

    private $types = [
        'blade.php',
        'scss',
        'js'
    ];

    public function __construct( $data, $destination ) {

        $this->files = new File;

        $this->data = $data;

        $this->destination = $destination;

    }


    public function getFiles($directory) {

        $files = $this->files->allFiles( __DIR__ . $directory );

        foreach( $files as $file ) {

            $this->processFile( $file );

        }

        return true;
    }


    public function processFile( $file ) {

        $fileName = substr($file->getFileName(), 0, strpos($file->getFileName(), ".stub"));

        $type = $this->getFileType( $fileName );

        $content = $this->getFileContent( $file->getPathName() );

        $this->makeDir( $this->destination . $file->getRelativePath() );

        $this->putFile( $this->destination  . $file->getRelativePath() . '/' . $fileName, $content );

    }


    private function getFileType( $name ) {

        foreach( $this->types as $type )
        {
            if (strpos($name, $type) !== FALSE) {
                return $type;
            }
        }

        return '';
    }


    private function getFileContent( $path ) {

        $stub = $this->files->get( $path );

        $content = $this->replaceThemeName( $stub );
        $content = $this->replaceThemeName( $content );

        return $content;
    }


    private function replaceThemeName( $stub ) {

        if ($data['themeName']) {

            return $this->replace($stub, $data['themeName'], 'themeName');

        }

        return $content;
    }


    private function replace($stub, $value, $option) {

        return str_replace('{{' . $option . '}}', $value, $stub);

    }


    private function putFile( $file, $cotent ) {

        $this->files->put($file, $cotent);

    }


    private function makeDir( $directory ) {

        if ( !$this->files->isDirectory( $directory ) ) {
            $this->files->makeDirectory( $directory, 0777, true);
        }

    }


}
