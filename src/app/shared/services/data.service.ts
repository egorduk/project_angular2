import { Injectable } from '@angular/core';
import { Http, Response, HTTP_PROVIDERS } from '@angular/http';
//Grab everything with import 'rxjs/Rx';
import { Observable } from 'rxjs/Observable';
import {Observer} from 'rxjs/Observer';
import 'rxjs/add/operator/map'; 
import 'rxjs/add/operator/catch';

import { ICustomer, IOrder, IState, IPicture } from '../interfaces';

@Injectable()
export class DataService {
  
    _baseUrl: string = '';
    customers: ICustomer[];
    orders: IOrder[];
    states: IState[];
    pictures: IPicture[];
    //body = JSON.stringify({action: "get_pictures"});
    //searchParams = new URLSearchParams();
    //searchParams.set('action', "get_pictures");

    constructor(private http: Http) { }

    load() {
        //this.http.get(this._baseUrl + 'app/server/server.php')
        this.http.get('http://localhost:80/project_angular2/src/app/server/server.php?action=get_pictures')
            .subscribe(res => console.log(res.json()));
        //var headers = new Headers();
        //headers.append('Authorization', 'test');

      /*  return this.http.get(this._baseUrl + 'app/server/server.php')
            .map((res: Response) => {
                console.log("Response came");
                console.log(res);
                //return res;
            })
            .catch(this.handleError);*/

       /* this.http.get({
            method: 'GET',
            url: this._baseUrl + 'app/server/server.php'
        }).then(function (response) {
            // code to execute in case of success
            console.log(response);
        }, function (response) {
            // code to execute in case of error
        });*/
    }

    getPictures() : Observable<IPicture[]> {
        if (!this.pictures) {
            //return this.http.get(this._baseUrl + 'app/server/server.php')
            return this.http.get('http://localhost:80/project_angular2/src/app/server/server.php?action=get_pictures')
            //return this.http.get(this._baseUrl + 'pictures.json')
                .map((res: Response) => {
                    this.pictures = res.json();
                    return this.pictures;
                })
                .catch(this.handleError);
        } else {
            //return cached data
            return this.createObservable(this.pictures);
        }
    }
    
    getCustomers() : Observable<ICustomer[]> {
        if (!this.customers) {
            return this.http.get(this._baseUrl + 'customers.json')
                        .map((res: Response) => {
                            this.customers = res.json();
                            return this.customers;
                        })
                        .catch(this.handleError);
        }
        else {
            //return cached data
            return this.createObservable(this.customers);
        }
    }
    
    getCustomer(id: number) : Observable<ICustomer> {
        if (this.customers) {
            //filter using cached data
            return this.findCustomerObservable(id);
        } else {
            //Query the existing customers to find the target customer
            return Observable.create((observer: Observer<ICustomer>) => {
                    this.getCustomers().subscribe((customers: ICustomer[]) => {
                        this.customers = customers;                
                        const cust = this.filterCustomers(id);
                        observer.next(cust);
                        observer.complete();
                })
            })
            .catch(this.handleError);
        }
    }

    getOrders(id: number) : Observable<IOrder[]> {
      return this.http.get(this._baseUrl + 'orders.json')
                .map((res: Response) => {
                    this.orders = res.json();
                    return this.orders.filter((order: IOrder) => order.customerId === id);
                })
                .catch(this.handleError);               
    }
    
    updateCustomer(customer: ICustomer) : Observable<boolean> {
        return Observable.create((observer: Observer<boolean>) => {
            this.customers.forEach((cust: ICustomer, index: number) => {
               if (cust.id === customer.id) {
                   const state = this.filterStates(customer.state.abbreviation);
                   customer.state.abbreviation = state.abbreviation;
                   customer.state.name = state.name;
                   this.customers[index] = customer;
               } 
            });
            observer.next(true);
            observer.complete();
        });
    }
    
    getStates(): Observable<IState[]> {
        if (this.states) {
            return Observable.create((observer: Observer<IState[]>) => {
                observer.next(this.states);
                observer.complete();
            });
        } else {
            return this.http.get(this._baseUrl + 'states.json').map((response: Response) => {
                this.states = response.json();
                return this.states;
            })
            .catch(this.handleError);
        }
    }
    
    private findCustomerObservable(id: number) : Observable<ICustomer> {        
        return this.createObservable(this.filterCustomers(id));
    }
    
    private filterCustomers(id: number) : ICustomer {
        const custs = this.customers.filter((cust) => cust.id === id);
        return (custs.length) ? custs[0] : null;
    }
    
    private createObservable(data: any) : Observable<any> {
        return Observable.create((observer: Observer<any>) => {
            observer.next(data);
            observer.complete();
        });
    }
    
    private filterStates(stateAbbreviation: string) {
        const filteredStates = this.states.filter((state) => state.abbreviation === stateAbbreviation);
        return (filteredStates.length) ? filteredStates[0] : null;
    }
    
    private handleError(error: any) {
        console.error(error);
        return Observable.throw(error.json().error || 'Server error');
    }

}
