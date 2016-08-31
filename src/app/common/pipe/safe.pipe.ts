import {Pipe} from '@angular/core';
import {DomSanitizationService} from '@angular/platform-browser';

@Pipe({name: 'safeBg'})

export class SafeBgPipe {
    constructor(private sanitizer:DomSanitizationService){}

    transform(image) {
        if (image) {
            return this.sanitizer.bypassSecurityTrustStyle('url(../../uploads/pictures/' + image + ')');
        }
    }
}

@Pipe({name: 'safeFileExt'})

export class SafeFileExtPipe {
    constructor(){}

    transform(filename) {
        if (filename) {
            return filename.replace(/.jpg|.gif|.jpeg|.png/, '');
        }
    }
}

@Pipe({name: 'getFileExtByFileType'})

export class GetFileExtByFileTypePipe {
    constructor(){}

    transform(fileType) {
        if (fileType) {
            let fileExt = '';

            if (fileType == 'image/jpeg') {
                fileExt = '.jpeg';
            } else if (fileType == 'image/png') {
                fileExt = '.png';
            } else if (fileType == 'image/gif') {
                fileExt = '.gif';
            }

            return fileExt;
        }
    }
}

@Pipe({name: 'getFileExtByFileName'})

export class GetFileExtByFileNamePipe {
    constructor(){}

    transform(fileName) {
        if (fileName) {
            return fileName.match(/.jpeg|.jpg|.gif|.png/);
        }
    }
}