(function(global) {

    // map tells the System loader where to look for things
    var map = {
        'app':                        'app', // 'dist',
        'rxjs':                       'node_modules/rxjs',
        '@angular':                   'node_modules/@angular',
        'angular2-localstorage':      'node_modules/angular2-localstorage',
        'angular2-image-popup':       'node_modules/angular2-image-popup/directives/angular2-image-popup',
        'angular2-jwt':               'node_modules/angular2-jwt',
        'ng2-file-upload':            'node_modules/ng2-file-upload',
        'ng2-select':                 'node_modules/ng2-select',
        'ng2-bootstrap':              'node_modules/ng2-bootstrap',
        'moment':                     'node_modules/moment',
        "ng2-modal":                  "node_modules/ng2-modal",
        'ts-md5':                     'node_modules/ts-md5'
    };

    // packages tells the System loader how to load when no filename and/or no extension
    var packages = {
        'app':                        { main: 'main.js',  defaultExtension: 'js' },
        'rxjs':                       { defaultExtension: 'js' },
        'angular2-localstorage': {
            defaultExtension: "js"
        },
        'angular2-jwt': {
            defaultExtension: "js"
        },
        'angular2-image-popup': {
            defaultExtension: "js"
        },
        'ng2-file-upload': {
            defaultExtension: "js"
        },
        'ng2-select': {
            defaultExtension: "js"
        },
        'ng2-bootstrap': {
            defaultExtension: "js"
        },
        'moment': {
            main: 'moment.js',
            defaultExtension: "js"
        },
        "ng2-modal": { "main": "index.js", "defaultExtension": "js" },
        'ts-md5': {main: 'md5.js', "defaultExtension": "js"}
    };

    var ngPackageNames = [
        '@angular/common',
        '@angular/compiler',
        '@angular/core',
        '@angular/http',
        '@angular/forms',
        '@angular/platform-browser',
        '@angular/platform-browser-dynamic',
        '@angular/router',
        '@angular/router-deprecated',
        '@angular/testing',
        '@angular/upgrade'
    ];

    // add package entries for angular packages in the form '@angular/common': { main: 'index.js', defaultExtension: 'js' }
    ngPackageNames.forEach(function(pkgName) {
        packages[pkgName] = { main: 'index.js', defaultExtension: 'js' };
    });

    var config = {
        map: map,
        packages: packages
    }

    // filterSystemConfig - index.html's chance to modify config before we register it.
    if (global.filterSystemConfig) { global.filterSystemConfig(config); }

    System.config(config);

})(this);