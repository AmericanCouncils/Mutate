<?php

namespace AC\Mutate;

class Job extends Preset {
    protected $name;
    protected $description;
	
    public function getRequiredPresets() {
        return array();
    }
    
    public function generateOutputReferences() {
        
    }
    
	public function configure($inFile) {
        $newFiles = array();
        
        //chain multiple presets on one file
        $this->addFile($this->runPreset('video_to_mp4_high', $inFile)->);
        $newFiles[] = $this->runPreset($newFiles[1], 'video_to_mp4_high');
        $newFiles[] = $this->runPreset($inFile, 'video_thumb_high');
        $newFiles[] = $this->runPreset($inFile, 'video_thumb_high');
        $newFiles[] = $this->runPreset($inFile, 'video_thumb_high');
        $newFiles[] = $this->runPreset($inFile, 'video_thumb_high');
    }
    
    public function execute($inFile, $outputFilePath) {
        
    }
    
    protected function addFile() {
    }
	
}
