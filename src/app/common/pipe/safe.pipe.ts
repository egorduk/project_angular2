import {Pipe} from '@angular/core';
import {DomSanitizationService} from '@angular/platform-browser';

@Pipe({name: 'safeBg'})

export class SafePipe {
    constructor(private sanitizer:DomSanitizationService){}

    transform(image) {
        if (image) {
            return this.sanitizer.bypassSecurityTrustStyle('url(../../uploads/pictures/' + image + ')');
        }
    }
}