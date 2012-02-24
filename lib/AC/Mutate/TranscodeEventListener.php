<?php

namespace AC\Mutate;

/**
 * Listeners contain specific methods that get called by the Transcoder with specific parameters during every transcode process.
 * 
 * Implementing and registering a listener would be the preferred way to provide logging, or any other type of "callback" functionality.
 *
 * Listeners are NOT meant to alter the Transcode process in anyway, they are meant to be passive observers - to the point that if any exceptions are thrown from
 * a listener, they will be caught and ignored.
 *
 * If you want to alter the internal processes of the Transcoder, you should extend and override the Transcoder as suits your needs.
 */
abstract class TranscodeEventListener {

	/**
	 * Gets called just before calling an adapter to initiate a transcode process.  The arguments received are the same as the adapter receives.
	 *
	 * @param File $inFile 
	 * @param Preset $preset 
	 * @param string $outputFilePath 
	 */
	public function onTranscodeStart(File $inFile, Preset $preset, $outputFilePath) {
		
	}

	/**
	 * Gets called once a transcode process completes successfully.  This is the last action that happens before the new file is returned from the transcode process.
	 *
	 * @param File $inFile 
	 * @param Preset $preset 
	 * @param File $outFile 
	 */
	public function onTranscodeComplete(File $inFile, Preset $preset, File $outFile) {
		
	}
	
	/**
	 * Gets called on any transcode failure.  All paramters that were present when the process started are passed, in addition to the Exception caught which stopped the process.
	 *
	 * Note that this method gets called AFTER the Transcoder has already cleaned up from a failed process - meaning the intended output file, if it even was created in the first place, may
	 * have already been deleted.
	 *
	 * @param File $inFile 
	 * @param Preset $preset 
	 * @param string $outputFilePath 
	 * @param Exception $e 
	 */
	public function onTranscodeFailure(File $inFile, Preset $preset, $outputFilePath, \Exception $e) {
		
	}
}