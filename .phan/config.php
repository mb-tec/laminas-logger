<?php

/**
 * This configuration will be read and overlaid on top of the
 * default configuration. Command line arguments will be applied
 * after this file is read.
 */
return [
    // Supported values: `'5.6'`, `'7.0'`, `'7.1'`, `'7.2'`, `'7.3'`, `'7.4'`, `null`.
    // If this is set to `null`,
    // then Phan assumes the PHP version which is closest to the minor version
    // of the php executable used to execute Phan.
    "target_php_version" => '7.4',

    // A list of directories that should be parsed for class and
    // method information. After excluding the directories
    // defined in exclude_analysis_directory_list, the remaining
    // files will be statically analyzed for errors.
    //
    // Thus, both first-party and third-party code being used by
    // your application should be included in this list.
    'directory_list' => [
        'src',
        'vendor',
    ],

    // A directory list that defines files that will be excluded
    // from static analysis, but whose class and method
    // information should be included.
    //
    // Generally, you'll want to include the directories for
    // third-party code (such as "vendor/") in this list.
    //
    // n.b.: If you'd like to parse but not analyze 3rd
    //       party code, directories containing that code
    //       should be added to the `directory_list` as
    //       to `exclude_analysis_directory_list`.
    "exclude_analysis_directory_list" => [
        'vendor/',
    ],

    // If enabled, Phan will warn if **any** type in a method invocation's object
    // is definitely not an object,
    // or if **any** type in an invoked expression is not a callable.
    // Setting this to true will introduce numerous false positives
    // (and reveal some bugs).
    'strict_method_checking' => true,

    // Set to true in order to attempt to detect unused variables.
    // `dead_code_detection` will also enable unused variable detection.
    //
    // This has a few known false positives, e.g. for loops or branches.
    'unused_variable_detection' => true,

    // If true, then run a quick version of checks that takes less time.
    // False by default.
    'quick_mode' => false,

    // Add any issue types (such as `'PhanUndeclaredMethod'`)
    // to this list to inhibit them from being reported.
    'suppress_issue_types' => [
        'PhanUnusedPublicMethodParameter',
        'PhanUnusedVariableCaughtException',
    ],

    // A list of plugin files to execute.
    // Plugins which are bundled with Phan can be added here by providing their name
    // (e.g. 'AlwaysReturnPlugin')
    //
    // Documentation about available bundled plugins can be found
    // at https://github.com/phan/phan/tree/master/.phan/plugins
    //
    // Alternately, you can pass in the full path to a PHP file
    // with the plugin's implementation (e.g. 'vendor/phan/phan/.phan/plugins/AlwaysReturnPlugin.php')
    'plugins' => [
        'AlwaysReturnPlugin',
        'DollarDollarPlugin',
        'DuplicateArrayKeyPlugin',
        'DuplicateExpressionPlugin',
        'PregRegexCheckerPlugin',
        'PrintfCheckerPlugin',
        'SleepCheckerPlugin',
        'UnreachableCodePlugin',
        'UseReturnValuePlugin',
        'EmptyStatementListPlugin',
        'LoopVariableReusePlugin',
    ],
];