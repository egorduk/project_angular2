import { Component, ElementRef, EventEmitter, Injectable, Input, Output } from '@angular/core';
import { Router } from '@angular/router';

import { GetFileExtByFileNamePipe } from '../common/pipe/safe.pipe';
import { FileUploader } from 'ng2-file-upload/ng2-file-upload';

import { DataService } from '../common/service/data.service';
import { ITag } from '../common/interfaces';
//import { User } from '../user/user';
import { MessageService } from '../common/service/message.service';
//import { SELECT_DIRECTIVES } from 'ng2-select/ng2-
import { tokenNotExpired, JwtHelper } from 'angular2-jwt/angular2-jwt';

const URL = 'http://localhost:80/project_angular2/api/pictures';
declare var $:any;

@Component({
    selector: 'header',
    styleUrls: ['app/header/style.css'],
    templateUrl: 'app/header/header.component.html'
})

export class HeaderComponent implements OnInit {
    private tags: ITag[] = [];
    private user: IUser;

    private _openUploader: boolean = false;
    public uploader: FileUploader /*= new FileUploader({url: URL, *//*authToken: this._token, *//*allowedMimeType: ['image/jpeg', 'image/gif', 'image/png']})*/;
    public hasBaseDropZoneOver: boolean = false;
    private _el: HTMLElement;
    private _token: string = '';
    //private _showSuccessMessage: boolean = false;
    private _getFileExtByFileNamePipe: GetFileExtByFileNamePipe;
    private value: any = [];
    private _tags: any = [];
    private _isLogged: boolean = false;
    private _currentUserId: number = 0;

    @Output() emitResponse: EventEmitter<string> = new EventEmitter<string>();

    private jwtHelper: JwtHelper = new JwtHelper();

    constructor(private router: Router, private el: ElementRef, private dataService: DataService, private ms: MessageService) {
        this._token = localStorage.getItem('id_token');
        this._isLogged = this._token && !this.jwtHelper.isTokenExpired(this._token);
        //console.log(this._isLogged);

        if (this._isLogged) {
            let data = window.jwt_decode(this._token);
            this._currentUserId = data.uid;
            this.getCurrentUserInfo(this._currentUserId);

            this._el = el.nativeElement;

            this.getTags();

            this.uploader = new FileUploader({url: URL, authToken: this._token, allowedMimeType: ['image/jpeg', 'image/gif', 'image/png']});

            this.uploader.onBeforeUploadItem = function(item) {
                if (item.file.newName) {
                    this._getFileExtByFileNamePipe = new GetFileExtByFileNamePipe();
                    item.file.name = item.file.newName + this._getFileExtByFileNamePipe.transform(item.file.name);
                }

                item.file.tags = (item.file.tags) ? item.file.tags.join(',') : null;
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
                    }
                }
            });
    }

    private getCurrentUserInfo(userId) {
        this.dataService.getUserInfoById(userId)
            .subscribe((user: IUser) => {
                if (user.response) {
                    if (user.user) {
                        this.user = user.user;
/*
                        if (this.user.id == this._currentUserId) {
                            this._isCurrentUser = true;
                        }

                        this.getUserPictures(this.user.id);*/
                    }
                } else {
                    this.router.navigate(['/home']);
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
    }

    public refreshValue(value:any):void {
        this.value = value;
    }

    public itemsToString(value:Array<any> = []):string {
        return value
            .map((item:any) => {
                return item.text;
            }).join(',');
    }

    public logout(event) {
        event.preventDefault();

        localStorage.removeItem('id_token');
        this._isLogged = false;

        this.router.navigate(['/login']);
    }

    public profile(event) {
        event.preventDefault();

        this.router.navigate(['/user', this.user.login]);
    }

    private onEmitter(): void {
        console.log('emit');
        this._isLogged = true;
    }

    //https://vladotesanovic.me/2016/02/01/angular-2-asyncpipe/
}