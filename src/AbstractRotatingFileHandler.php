<?php declare(strict_types=1);

namespace Monolog\Handler;

use Monolog\Level;
use Monolog\Utils;

abstract class AbstractRotatingFileHandler extends StreamHandler
{
  protected string $filename;
  protected bool|null $mustRotate = null;
  protected \DateTimeImmutable $nextRotation;
  protected array $rotateSettings;

  public function __construct(
    int|string|Level $level = Level::Debug,
    bool $bubble = false,
    array $rotateSettings,
    string $filename,
    ?int $filePermission = null,
    bool $useLocking = false
  ) {
    $this->filename       = Utils::canonicalizePath( $filename );
    $this->rotateSettings = $rotateSettings;
    $this->nextRotation   = $this->getNextRotation();

    parent::__construct( $this->filename, $level, $bubble, $filePermission, $useLocking );
  }
  /**
   * @inheritDoc
   */
  public function close(): void
  {
    parent::close();

    if( true === $this->mustRotate ) {
      $this->rotate();
    }
  }

  /**
   * @inheritDoc
   */
  public function reset(): void
  {
    parent::reset();

    if( true === $this->mustRotate ) {
      $this->rotate();
    }
  }
  /**
   * @inheritDoc
   */
  protected function write( array $record ): void
  {
    if( true === $this->mustRotate ) {
      $this->rotate();
    }

    parent::write( $record );
  }

  abstract protected function rotate(): void;

  abstract protected function getNextRotation(): \DateTimeImmutable;


}