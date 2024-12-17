# Composer Patch Clone

This is a composer plugin that allows you to clone a patch from a remote repository and apply it to your project.

The plugin uses the [cweagans/composer-patches](https://github.com/cweagans/composer-patches) plugin to apply the patch.

## Installation

```bash
composer require idimopoulos/composer-patch-clone
```

## Usage

```bash
composer patch-clone some-vendor/some-package "Some title for the patch" some-patch-uri
```

## Configuration

The plugin offers the `output-dir` configuration option to specify the directory where the patch will be saved. The default value is `patches`.

```json
{
    "extra": {
        "composer-patch-clone": {
            "output-dir": "patches"
        }
    }
}
```

By default, the output directory is in the `resources/patch` directory and are
structured as follows:

```
resources/patch
└── some-vendor
    └── some-package
        └── some-patch-file.patch
```

## Configuration options

- `output-name`: The name of the patch file. The default value is the last part
of the patch URI.

## License

GPL-3.0
