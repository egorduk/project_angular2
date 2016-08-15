(function(global) {

    // map tells the System loader where to look for things
    var map = {
        'app':                        'app', // 'dist',
        'rxjs':                       'node_modules/rxjs',
        '@angular':                   'node_modules/@angular',
        'angular2-localstorage':      'node_modules/angular2-localstorage',
        'angular2-jwt':               'node_modules/angular2-jwt',
        'angular2-image-popup':       'node_modules/angular2-image-popup/directives/angular2-image-popup'
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
        }
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