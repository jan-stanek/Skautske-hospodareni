<?php

declare(strict_types=1);

namespace Model\Infrastructure\Services\Common;

use Codeception\Test\Unit;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Model\Common\Exception\InvalidScanFile;
use Model\Common\FileNotFound;
use Nette\Utils\FileSystem as FileSystemUtil;
use Nette\Utils\Image;
use function uniqid;

final class FlysystemScanStorageTest extends Unit
{
    /** @var  string */
    private $directory;

    /** @var FlysystemScanStorage */
    private $storage;

    protected function _before() : void
    {
        $this->directory = __DIR__ . '/../../../../_temp/' . uniqid(self::class, true);
        $this->storage   = new FlysystemScanStorage(new Filesystem(new Local($this->directory)));
    }

    protected function _after() : void
    {
        FileSystemUtil::delete($this->directory);
    }

    public function testSaveFile() : void
    {
        $contents = Image::fromBlank(1, 1)->toString();

        $this->storage->save('foo', $contents);

        $this->assertSame(
            $contents,
            FileSystemUtil::read($this->directory . '/foo'),
        );
    }

    public function testGetThrowsExceptionIfFileDoesNotExist() : void
    {
        $this->expectException(FileNotFound::class);

        $this->storage->get('unknown-file.jpg');
    }

    public function testDeleteRemovesFile() : void
    {
        $this->storage->save('foo', Image::fromBlank(1, 1)->toString());
        $this->storage->delete('foo');

        $this->assertFileNotExists($this->directory . '/foo');
    }

    public function testDeleteWithNonexistentFileDoesNothing() : void
    {
        $this->storage->delete('unknown-file.jpg');
    }

    public function testGetReturnsCorrectFile() : void
    {
        $contents = Image::fromBlank(1, 1)->toString();
        $this->storage->save('test/foo.jpg', $contents);

        $file = $this->storage->get('test/foo.jpg');

        $this->assertSame('test/foo.jpg', $file->getPath());
        $this->assertSame('foo.jpg', $file->getFileName());
        $this->assertSame($contents, (string) $file->getContents());
    }

    /**
     * @dataProvider dataInvalidContents
     */
    public function testCannotSaveFileWithInvalidMimeType(string $contents) : void
    {
        $this->expectException(InvalidScanFile::class);

        $this->storage->save('foo', $contents);
    }

    /**
     * @return string[][]
     */
    public function dataInvalidContents() : array
    {
        return [
            [Image::fromBlank(1, 1)->toString(Image::GIF)],
            [''],
        ];
    }
}