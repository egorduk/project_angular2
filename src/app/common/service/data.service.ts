import { Injectable } from '@angular/core';
import { Http, Response, HTTP_PROVIDERS } from '@angular/http';
import { Observable } from 'rxjs/Observable';
import {Observer} from 'rxjs/Observer';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';
import { AuthHttp } from 'angular2-jwt/angular2-jwt';
import { IPicture, IUser } from '../interfaces';

@Injectable()
export class DataService {

    _baseUrl: string = '';
    _serverUrl: string = '';
    pictures: IPicture[];
    users: IUser[];

    constructor(private http: Http, private authHttp: AuthHttp) {
        this._serverUrl = 'http://localhost:80/project_angular2';
    }

    getFriendsPictures() : Observable<IPicture[]> {
        return this.authHttp.get(this._serverUrl + '/api/get_friends_pictures')
            .map((res: Response) => {
                this.pictures = res.json();
                //console.log(this.pictures);
                return this.pictures;
            })
            .catch(this.handleError);
    }

    getUnfollowUsers() : Observable<IUser[]> {
        return this.authHttp.get(this._serverUrl + '/api/get_unfollow_users')
            .map((res: Response) => {
                this.users = res.json();
                //console.log(this.users);
                return this.users;
            })
            .catch(this.handleError);
    }

    private handleError(error: any) {
        console.error(error);
        return Observable.throw(error.json().error || 'Server error');
    }

}