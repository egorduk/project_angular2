import { Component, OnInit, ElementRef } from '@angular/core';
import { Router, ROUTER_DIRECTIVES } from '@angular/router';
import { CORE_DIRECTIVES } from '@angular/common';
import { Http } from '@angular/http';
import { Observable } from 'rxjs/Observable';
import {Observer} from 'rxjs/Observer';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';
import { DataService } from '../common/service/data.service';
import { IUser, IPicture } from '../common/interfaces';
import { SafePipe } from '../common/pipe/safe.pipe';
//import { Subscription } from 'rxjs/Subscription';

declare  var $:any;

@Component({
    selector: 'user',
    directives: [ ROUTER_DIRECTIVES, CORE_DIRECTIVES ],
    pipes: [ SafePipe ],
    styleUrls: ['app/user/style.css'],
    templateUrl: 'app/user/user.html'
})

export class User implements OnInit {

    //private sub: Subscription;
    private _userLogin: string = '';
    private _userId: number = 0;
    private _isCurrentUser: boolean = false;
    private user: IUser[];
    private pictures: IPicture[] = [];
    private el: HTMLElement;

    constructor(private router: Router, private http: Http, private dataService: DataService, private el: ElementRef) {
        this.el = el.nativeElement;

        let token = localStorage.getItem('id_token');
        let data = window.jwt_decode(token);
        this._userId = data.uid;
       // this.router.navigate(['/hero', hero.id]);
        //this._userLogin = this.router.url.split('/')[2];

        //this.getUserPictures();

    }

    ngOnInit() {
        this._userLogin = this.router.url.split('/')[2];
        //let ul = this.router.url.split('/')[2];
        this.getUserInfo();


       // console.log($(this.el).find('#tiles li'));
        let imgs = $(this.el).find('#tiles li');
        let options = {
            autoResize: true, // This will auto-update the layout when the browser window is resized.
            container: $('#main'), // Optional, used for some extra CSS styling
            offset: 5, // Optional, the distance between grid items
            itemWidth: 460 // Optional, the width of a grid item
        };
        imgs.wookmark(options);
    }

    ngAfterViewInit() {
        //if (!Component.chosenInitialized) {
            /*var el:any = elelemtRef.domElement.children[0];
            $(el).chosen().on('change', (e, args) => {
                _this.selectedValue = args.selected;
            });*/
            //console.log($(this.el/*.domElement.children[0]).find('#tiles li')*/);
            //console.log($(this.el.nativeElement)/*.find('#tiles li')*/);
            //Component.chosenInitialized = true;
        //}
    }

    getUserInfo() {
        this.dataService.getUserInfo(this._userLogin)
            .subscribe((user: IUser[]) => {
                if (user.response) {
                    //console.log(user);
                    if (user.user) {
                        this.user = user.user;

                        if (this.user.id == this._userId) {
                            this._isCurrentUser = true;
                        }

                        //console.log(this.user.id);
                        this.getUserPictures(this.user.id);
                    }
                } else {
                    this.router.navigate(['/home']);
                }
            });

        //console.log(this.user);
    }

    getUserPictures(userId) {
        this.dataService.getUserPictures(userId)
            .subscribe((pictures: IPicture[]) => {
                this.pictures = (pictures.response) ? pictures.pictures : null;

                if (this.pictures) {
                    /*this.pictures.forEach((value: any, key: any) => {
                        if (value.gallery_ids) {
                            this.pictures[key].gallery_ids = value.gallery_ids.split(',');
                        }
                    });*/
                }

                //console.log('this.pictures', this.pictures);
            });
    }
}