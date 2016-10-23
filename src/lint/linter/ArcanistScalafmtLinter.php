<?php

final class ArcanistScalafmtLinter extends ArcanistExternalLinter {

  private $jarPath = null;
  private $configPath = null;

  public function getInfoURI() {
    return 'https://olafurpg.github.io/scalafmt/';
  }

  public function getInfoDescription() {
    return 'Check that Scala code has been formatted with scalafmt';
  }

  public function getLinterName() {
    return 'scalafmt';
  }

  public function getLinterConfigurationName() {
    return 'scalafmt';
  }

  public function getDefaultBinary() {
    return 'scalafmt';
  }

  public function getInstallInstructions() {
    return 'Run `brew install olafurpg/scalafmt/scalafmt`';
  }

  protected function getMandatoryFlags() {
    return array('--test');
  }

  protected function parseLinterOutput($path, $err, $stdout, $stderr) {
    return array(
      id(new ArcanistLintMessage())
        ->setName($this->getLinterName())
        ->setPath($path)
        ->setCode($this->getLinterName())
        ->setSeverity(ArcanistLintSeverity::SEVERITY_ERROR)
        ->setDescription('Incorrectly formatted file: ' . $path)
    );
  }

  public function getLinterConfigurationOptions() {
    $options = array(
      'config' => array(
        'type' => 'optional string',
        'help' => pht(
          'String containing scalafmt configuration, or path to a '.
          'configuration file')
      ),
    );

    return $options + parent::getLinterConfigurationOptions();
  }

  public function setLinterConfigurationValue($key, $value) {
    switch ($key) {
      case 'config':
        $working_copy = $this->getEngine()->getWorkingCopy();
        $root = $working_copy->getProjectRoot();

        $path = $value;

        if (Filesystem::pathExists($path)) {
          $this->configPath = $path;
          return;
        }

        $path = Filesystem::resolvePath($path, $root);

        if (Filesystem::pathExists($path)) {
          $this->configPath = $path;
          return;
        }

        throw new ArcanistUsageException(
          pht('None of the configured Scalafmt configs can be located.'));
    }

    return parent::setLinterConfigurationValue($key, $value);
  }

}
