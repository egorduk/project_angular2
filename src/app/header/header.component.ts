import { Component, ElementRef, EventEmitter, Injectable, Input, Output } from '@angular/core';
import { CORE_DIRECTIVES } from '@angular/common';
import { Http, Headers } from '@angular/http';
import { Router, ROUTER_DIRECTIVES } from '@angular/router';

import { GetFileExtByFileNamePipe } from '../common/pipe/safe.pipe';
import { FileUploader } from 'ng2-file-upload/ng2-file-upload';

import { DataService } from '../common/service/data.service';
import { ITag } from '../common/interfaces';
//import { User } from '../user/user';
import { MessageService } from '../common/service/message.service';
//import { SELECT_DIRECTIVES } from 'ng2-select/ng2-select';

const URL = 'http://localhost:80/project_angular2/api/pictures';
declare var $:any;

@Component({
    selector: 'header',
    //directives: [ CORE_DIRECTIVES, ROUTER_DIRECTIVES, SELECT_DIRECTIVES ],
    //providers: [ GetFileExtByFileNamePipe ],
    styleUrls: ['app/header/style.css'],
    //pipes: [ FileSizePipe, SafeFileExtPipe ],
    templateUrl: 'app/header/header.component.html'
})

export class HeaderComponent implements OnInit {
    /*jwt: string;
     decodedJwt: string;
     response: string;
     api: string;
     _serverUrl: string = '';*/
    private _openUploader: boolean = false;
    public uploader: FileUploader /*= new FileUploader({url: URL, *//*authToken: this._token, *//*allowedMimeType: ['image/jpeg', 'image/gif', 'image/png']})*/;
    public hasBaseDropZoneOver: boolean = false;
    private _el: HTMLElement;
    private _token: string = '';
    //private _showSuccessMessage: boolean = false;
    private tags: ITag[] = [];
    private _getFileExtByFileNamePipe: GetFileExtByFileNamePipe;
    private value:any = [];
    private _tags:any = [];
    message: any = [];
    piy: boolean = false;

    @Output() emitResponse: EventEmitter<string> = new EventEmitter<string>();

    constructor(public router: Router,
                public http: Http,
                private el: ElementRef,
                private dataService: DataService,
                private ms: MessageService) {
        this._token = localStorage.getItem('id_token');
        this._el = el.nativeElement;

        this.getTags();

        this.uploader = new FileUploader({url: URL, authToken: this._token, allowedMimeType: ['image/jpeg', 'image/gif', 'image/png']});

        this.uploader.onBeforeUploadItem = function(item) {
            if (item.file.newName) {
                this._getFileExtByFileNamePipe = new GetFileExtByFileNamePipe();
                item.file.name = item.file.newName + this._getFileExtByFileNamePipe.transform(item.file.name);
            }

            item.file.tags = (item.file.tags) ? item.file.tags.join(',') : null;
            //console.log(item.file);
        };

        this.uploader.onAfterAddingFile = function(item) {
            this.showSuccessMessage = false;
        };

        let that = this;

        this.uploader.onCompleteAll = function() {
            this.showSuccessMessage = true;
            that.emitResponse.emit("");
        };

        this.emitResponse = this.ms.rxEmitter;
    }

    ngAfterViewChecked() {
    }

    ngAfterViewInit() {
    }

    private showUploader(event) {
        event.preventDefault();

        this._openUploader = !this._openUploader;
        //this._showSuccessMessage = false;
    }

    public fileOverBase(e:any):void {
        this.hasBaseDropZoneOver = e;
    }

    private getTags() {
        //this.tags = null;
        this.dataService.getTags()
            .subscribe((tags: ITag[]) => {
                if (tags.response) {
                    if (tags.tags) {
                        tags.tags.forEach((value: any, key: any) => {
                            let obj = new Object();
                            obj.text = value.name;
                            obj.id = value.id;
                            this.tags[key] = obj;
                        });
                        //console.log(this.tags);
                    }
                }
            });
    }

    public selected(value:any, uploader):void {
        if (typeof uploader.file.tags === 'undefined') {
            uploader.file.tags = [];
        }

        uploader.file.tags.push(value.id);
    }

    public removed(value:any):void {
        //console.log('Removed value is: ', value);
    }

    public refreshValue(value:any):void {
        //console.log('Refresh value is: ', value);
        this.value = value;
    }

    public itemsToString(value:Array<any> = []):string {
        //console.log(value);
        return value
            .map((item:any) => {
                return item.text;
            }).join(',');
    }

    //https://vladotesanovic.me/2016/02/01/angular-2-asyncpipe/
}