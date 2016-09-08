import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { DataService } from '../common/service/data.service';

@Component({
    selector: 'signup',
    styleUrls: ['app/signup/style.css'],
    templateUrl: 'app/signup/signup.component.html'
})

export class SignupComponent {

    constructor(private router: Router, private dataService: DataService) {
    }

    signup(event, email, password) {
        event.preventDefault();
        console.log(email);
        console.log(password);

        this.dataService.signup(email, password)
            .subscribe((response: any) => {
                if (response.response) {
                    localStorage.setItem('id_token', response.id_token);
                    this.router.navigate(['/home']);
                } else {

                }
            });
    }

    login(event) {
        event.preventDefault();
        this.router.navigate(['/login']);
    }

}