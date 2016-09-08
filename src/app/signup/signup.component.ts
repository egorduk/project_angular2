import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { DataService } from '../common/service/data.service';

@Component({
    selector: 'signup',
    styleUrls: ['app/signup/style.css'],
    templateUrl: 'app/signup/signup.component.html'
})

export class SignupComponent {

    private successAlert : Object =  {
        type: 'success',
        msg: 'Yor was registered then login!',
        is_show: false
    };

    private dangerAlert : Object =  {
        type: 'danger',
        msg: 'Something is wrong!',
        is_show: false
    };

    constructor(private router: Router, private dataService: DataService) {

    }

    signup(event, email, password) {
        event.preventDefault();

        this.dataService.signup(email, password)
            .subscribe((response: any) => {
                if (response.response) {
                    //localStorage.setItem('id_token', response.id_token);
                    this.successAlert.is_show = true;
                    this.successAlert.type = 'success';
                    this.dangerAlert.is_show = false;

                    //this.router.navigate(['/home']);
                } else {
                    this.dangerAlert.is_show = true;
                    this.dangerAlert.type = 'danger';
                    this.successAlert.is_show = false;
                }
            });
    }

    login(event) {
        event.preventDefault();

        this.router.navigate(['/login']);
    }

    public closeAlert():void {
        //this.alerts.splice(i, 1);
    }

    /* public alert: Object = [
     {
     type: 'success',
     msg: 'Well done! You successfully read this important alert message.',
     closable: true
     }
     ];*/

}