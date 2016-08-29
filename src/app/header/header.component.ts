import { Component, ElementRef } from '@angular/core';
import { CORE_DIRECTIVES } from '@angular/common';
import { Http, Headers } from '@angular/http';
import { Router, ROUTER_DIRECTIVES } from '@angular/router';
import { FileSizePipe } from '../common/pipe/fileSize.pipe';
import { SafeFileExtPipe } from '../common/pipe/safe.pipe';
import { GetFileExtByFileTypePipe } from '../common/pipe/safe.pipe';
import { FILE_UPLOAD_DIRECTIVES, FileUploader } from 'ng2-file-upload/ng2-file-upload';
import { DataService } from '../common/service/data.service';
import { ITag } from '../common/interfaces';

const URL = 'http://localhost:80/project_angular2/api/pictures';
declare var $:any;

@Component({
    selector: 'header',
    directives: [ CORE_DIRECTIVES, ROUTER_DIRECTIVES, FILE_UPLOAD_DIRECTIVES ],
    providers: [ GetFileExtByFileTypePipe ],
    styleUrls: ['app/header/style.css'],
    pipes: [ FileSizePipe, SafeFileExtPipe ],
    templateUrl: 'app/header/header.component.html'
})

export class HeaderComponent implements OnInit {
    /*jwt: string;
     decodedJwt: string;
     response: string;
     api: string;
     _serverUrl: string = '';*/
    private _openUploader: boolean = true;
    public uploader: FileUploader /*= new FileUploader({url: URL, *//*authToken: this._token, *//*allowedMimeType: ['image/jpeg', 'image/gif', 'image/png']})*/;
    public hasBaseDropZoneOver: boolean = false;
    private _el: HTMLElement;
    private _token: string = '';
    private _showSuccessMessage: boolean = false;
    private tags: ITag[] = [];

    constructor(public router: Router, public http: Http, private getFileExtByFileTypePipe: GetFileExtByFileTypePipe, private el: ElementRef, private dataService: DataService) {
        /* this.jwt = localStorage.getItem('id_token');
         this.decodedJwt = this.jwt && window.jwt_decode(this.jwt);
         this._serverUrl = 'http://localhost:80/project_angular2';
         this.getAllUserPictures();*/

        this._token = localStorage.getItem('id_token');
        this._el = el.nativeElement;
        //this.eventEmitter = new EventEmitter<boolean>();

        this.uploader = new FileUploader({url: URL, authToken: this._token, allowedMimeType: ['image/jpeg', 'image/gif', 'image/png']});
        //console.log(this.getFileExtByFileTypePipe);

       // this.uploader.authToken = this._token;

        this.uploader.onBeforeUploadItem = function(item) {
            if (item.file.newName) {
                this.getFileExtByFileTypePipe = new GetFileExtByFileTypePipe();
                item.file.name = item.file.newName + this.getFileExtByFileTypePipe.transform(item.file.type);
            }
        };

        this.uploader.onAfterAddingFile = function(item) {
            this.showSuccessMessage = false;
        };

        this.uploader.onCompleteAll = function() {
            this.showSuccessMessage = true;
        }


    }

    ngAfterViewChecked() {
        //console.log($(this.el).find('.selectpicker'));
        $(this._el).find('.selectpicker').selectpicker({
            style: 'btn-default',
            //width: 'auto'
            //size: 10
        });
    }

    showUploader(event) {
        event.preventDefault();

        this._openUploader = !this._openUploader;
        this._showSuccessMessage = false;

        this.getTags();
    }

    public fileOverBase(e:any):void {
        this.hasBaseDropZoneOver = e;
    }

    getTags() {
        this.dataService.getTags()
            .subscribe((tags: ITag[]) => {
                if (tags.response) {
                    if (tags.tags) {
                        this.tags = tags.tags;
                    }
                }
            });
    }
}
