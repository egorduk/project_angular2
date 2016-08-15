import { Injectable } from '@angular/core';

@Injectable()
export class GlobalService {

    _apiUrl: string = '';

    constructor() {
        this._apiUrl = 'http://localhost:80/project_angular2/api';
    }

    public getApiUrl() {
        return this._apiUrl;
    }
}