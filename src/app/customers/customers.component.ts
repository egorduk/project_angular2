import { Component, OnInit } from '@angular/core';
import { ROUTER_DIRECTIVES } from '@angular/router';
//import { Observable } from 'rxjs/Observable';

import { DataService } from '../shared/services/data.service';
import { FilterTextboxComponent } from '../filterTextbox/filterTextbox.component';
import { CustomersCardComponent } from './customersCard.component';
import { CustomersGridComponent } from './customersGrid.component'
import { PicturesComponent } from './pictures.component'
import { ICustomer, IOrder, IPicture } from '../shared/interfaces';

@Component({
    moduleId: module.id,
    selector: 'customers',
    templateUrl: 'customers.component.html',
    directives: [ ROUTER_DIRECTIVES, FilterTextboxComponent, CustomersCardComponent, CustomersGridComponent, PicturesComponent ]
})

export class CustomersComponent implements OnInit {

    title: string;
    filterText: string;
    customers: ICustomer[] = [];
    pictures: IPicture[] = [];
    filteredCustomers: ICustomer[] = [];
    filteredPictures: IPicture[] = [];
    displayMode: DisplayModeEnum;
    displayModeEnum = DisplayModeEnum;

    constructor(private dataService: DataService) { }

    ngOnInit() {
        this.title = 'Customers';
        this.filterText = 'Filter Customers:';
        this.displayMode = DisplayModeEnum.Card;

        this.dataService.getCustomers()
            .subscribe((customers: ICustomer[]) => {
                this.customers = this.filteredCustomers = customers;
            });

        this.dataService.getPictures()
            .subscribe((pictures: IPicture[]) => {
                //console.log(pictures);
                this.pictures = this.filteredPictures = pictures;
            });
    }

    changeDisplayMode(mode: DisplayModeEnum) {
        this.displayMode = mode;
    }

    filterChanged(data: string) {
        if (data && this.customers) {
            data = data.toUpperCase();
            let props = ['firstName', 'lastName', 'address', 'city', 'orderTotal'];
            let filtered = this.customers.filter(item => {
                let match = false;
                for (let prop of props) {
                    //console.log(item[prop] + ' ' + item[prop].toUpperCase().indexOf(data));
                    if (item[prop].toString().toUpperCase().indexOf(data) > -1) {
                        match = true;
                        break;
                    }
                };
                return match;
            });
            this.filteredCustomers = filtered;
        }
        else {
            this.filteredCustomers = this.customers;
        }
    }

}

enum DisplayModeEnum {
    Card = 0,
    Grid = 1,
    Map = 2
}
