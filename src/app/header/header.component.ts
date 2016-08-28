import { Component, ElementRef } from '@angular/core';
import { CORE_DIRECTIVES } from '@angular/common';
import { Http, Headers } from '@angular/http';
import { Router, ROUTER_DIRECTIVES } from '@angular/router';
import { AuthHttp } from 'angular2-jwt/angular2-jwt';
import { FileSizePipe } from '../common/pipe/fileSize.pipe';
import { SafeFileExtPipe } from '../common/pipe/safe.pipe';
import { GetFileExtByFileTypePipe } from '../common/pipe/safe.pipe';
import {FILE_UPLOAD_DIRECTIVES, FileUploader} from 'ng2-file-upload/ng2-file-upload';

const URL = 'http://localhost:80/project_angular2/api/pictures';
declare var $:any;

@Component({
    selector: 'header',
    directives: [ CORE_DIRECTIVES, ROUTER_DIRECTIVES, FILE_UPLOAD_DIRECTIVES ],
    providers: [ GetFileExtByFileTypePipe ],
    styleUrls: ['app/header/style.css'],
    pipes: [ FileSizePipe, SafeFileExtPipe, GetFileExtByFileTypePipe ],
    templateUrl: 'app/header/header.component.html'
})

export class HeaderComponent {
    /*jwt: string;
     decodedJwt: string;
     response: string;
     api: string;
     _serverUrl: string = '';*/
    private _openUploader: boolean = true;
    public uploader: FileUploader;
    public hasBaseDropZoneOver: boolean = false;
    private el: HTMLElement;

    constructor(public router: Router, public http: Http, private authHttp: AuthHttp, private getFileExtByFileTypePipe: GetFileExtByFileTypePipe, private el: ElementRef) {
        /* this.jwt = localStorage.getItem('id_token');
         this.decodedJwt = this.jwt && window.jwt_decode(this.jwt);
         this._serverUrl = 'http://localhost:80/project_angular2';
         this.getAllUserPictures();*/
        this.el = el.nativeElement;


        this.uploader = new FileUploader({url: URL, authToken: this._token, allowedMimeType: ['image/jpeg', 'image/gif', 'image/png']});
        //console.log(this.getFileExtByFileTypePipe);

        this.uploader.onBeforeUploadItem = function(item) {
            if (item.file.newName) {
                this.getFileExtByFileTypePipe = new GetFileExtByFileTypePipe();
                item.file.name = item.file.newName + this.getFileExtByFileTypePipe.transform(item.file.type);
            }

            //console.log(item.file);
        }

    }

    ngAfterViewChecked() {
        //console.log($(this.el).find('.selectpicker'));
        $(this.el).find('.selectpicker').selectpicker({
            style: 'btn-default',
            //width: 'auto'
            //size: 10
        });
    }

    showUploader(event) {
        event.preventDefault();

        this._openUploader = !this._openUploader;
    }

    public fileOverBase(e:any):void {
        this.hasBaseDropZoneOver = e;
    }

    /*
        setFilename(elInputFilename) {
            //console.log(this.uploader.getReadyItems());


            this.uploader.onAfterAddingAll = function(items) {
                items.forEach((value: any, key: any) => {
                    //value.file.name = elInputFilename.value;
                });

                //console.log(items);
            };
        }*/
}
