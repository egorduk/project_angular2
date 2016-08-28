import {Pipe} from '@angular/core';
import {DomSanitizationService} from '@angular/platform-browser';

@Pipe({name: 'fileSize'})

export class FileSizePipe {
    constructor(private sanitizer:DomSanitizationService){}

    transform(size) {
        if (size) {
            let i = Math.floor(Math.log(size) / Math.log(1024));
            return (size / Math.pow(1024, i)).toFixed(2) + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
        }
    }
}