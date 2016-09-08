import { Component } from '@angular/core';
import { Router } from '@angular/router';

import { DataService } from '../common/service/data.service';
import { Md5 } from 'ts-md5/dist/md5';

@Component({
    selector: 'login',
    styleUrls: ['app/login/style.css'],
    templateUrl: 'app/login/login.component.html'
})

export class LoginComponent {

    private dangerAlert : Object =  {
        type: 'danger',
        msg: 'There is no user with such email and password!',
        is_show: false
    };

    constructor(private router: Router, private dataService: DataService, private md5: Md5) {
    }

    login(event, email, password) {
        event.preventDefault();

        this.md5.start();
        this.md5.appendAsciiStr(password);
        password = this.md5.end();

        this.dataService.login(email, password)
            .subscribe((response: any) => {
                if (response.response) {
                    localStorage.setItem('id_token', response.token);
                    this.dangerAlert.is_show = false;

                    this.router.navigate(['/home']);
                } else {
                    this.dangerAlert.is_show = true;
                }
            });
    }

    signup(event) {
        event.preventDefault();

        this.router.navigate(['/signup']);
    }
}