import { Injectable } from '@angular/core';
import { Http, Response, HTTP_PROVIDERS } from '@angular/http';
import { Observable } from 'rxjs/Observable';
import {Observer} from 'rxjs/Observer';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';
import { AuthHttp } from 'angular2-jwt/angular2-jwt';
import { contentHeaders } from '../headers';
import { IPicture, IUser } from '../interfaces';
import { GlobalService } from './global.service';

@Injectable()
export class DataService {

    _baseUrl: string = '';
    _apiUrl: string = '';
    pictures: IPicture[];
    users: IUser[];

    constructor(private http: Http, private authHttp: AuthHttp, private globalService: GlobalService) {
        this._apiUrl = this.globalService.getApiUrl();
    }

    getFriendsPictures() : Observable<IPicture[]> {
        return this.authHttp.get(this._apiUrl + '/get_friends_pictures')
            .map((response: Response) => {
                this.pictures = response.json();
                //console.log(this.pictures);
                return this.pictures;
            })
            .catch(this.handleError);
    }

    getUnfollowUsers() : Observable<IUser[]> {
        return this.authHttp.get(this._apiUrl + '/get_unfollow_users')
            .map((response: Response) => {
                this.users = response.json();
                //console.log(this.users);
                return this.users;
            })
            .catch(this.handleError);
    }

    followUser(id: number) : Observable<boolean> {
        let body = JSON.stringify({ id });
        return this.authHttp.post(this._apiUrl + '/follow_user', body, { headers: contentHeaders })
            .map((response: Response) => {
                return response.json();
            })
            .catch(this.handleError);
    }

    getUnfollowUser() : Observable<IUser> {
        return this.authHttp.get(this._apiUrl + '/get_unfollow_users')
            .map((response: Response) => {
                this.users = response.json();
                //console.log(this.users);
                return this.users;
            })
            .catch(this.handleError);
    }

    likePicture(pictureId: number) : Observable<boolean> {
        let body = JSON.stringify({ pictureId });
        return this.authHttp.post(this._apiUrl + '/likes', body, { headers: contentHeaders })
            .map((response: Response) => {
                return response.json();
            })
            .catch(this.handleError);
    }

    private handleError(error: any) {
        console.error(error);
        return Observable.throw(error.json().error || 'Server error');
    }
}