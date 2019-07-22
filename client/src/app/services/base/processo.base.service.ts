/**
 *
 *
  _____                      _              _ _ _     _   _     _        __ _ _
 |  __ \                    | |            | (_) |   | | | |   (_)      / _(_) |
 | |  | | ___    _ __   ___ | |_    ___  __| |_| |_  | |_| |__  _ ___  | |_ _| | ___
 | |  | |/ _ \  | '_ \ / _ \| __|  / _ \/ _` | | __| | __| '_ \| / __| |  _| | |/ _ \
 | |__| | (_) | | | | | (_) | |_  |  __/ (_| | | |_  | |_| | | | \__ \ | | | | |  __/
 |_____/ \___/  |_| |_|\___/ \__|  \___|\__,_|_|\__|  \__|_| |_|_|___/ |_| |_|_|\___|

 * DO NOT EDIT THIS FILE!!
 *
 *  FOR CUSTOMIZE processoBaseService PLEASE EDIT ../processo.service.ts
 *
 *  -- THIS FILE WILL BE OVERWRITTEN ON THE NEXT SKAFFOLDER'S CODE GENERATION --
 *
 */
 // DEPENDENCIES
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { HttpClient } from '@angular/common/http';

// CONFIG
import { environment } from '../../../environments/environment';

// MODEL
import { Processo } from '../../domain/movarisch_db/processo';

/**
 * THIS SERVICE MAKE HTTP REQUEST TO SERVER, FOR CUSTOMIZE IT EDIT ../Processo.service.ts
 */

/*
 * SCHEMA DB Processo
 *
	{
		AltaEmissione: {
			type: 'Boolean',
			required : true
		},
		Nome: {
			type: 'String',
			required : true
		},
		//RELATIONS
		//EXTERNAL RELATIONS
		Sostanza: [{
			type: Schema.ObjectId,
			ref : "Processo"
		}],
	}
 *
 */
@Injectable()
export class ProcessoBaseService {

    contextUrl: string = environment.endpoint + '/processo';
    constructor(
        protected http: HttpClient
        ) { }

    // CRUD METHODS

    /**
    * ProcessoService.create
    *   @description CRUD ACTION create
    *
    */
    create(item: Processo): Observable<Processo> {
        return this.http
            .post<Processo>(this.contextUrl, item)
            .pipe(map(data => data));
    }

    /**
    * ProcessoService.delete
    *   @description CRUD ACTION delete
    *   @param ObjectId id Id
    *
    */
    remove(id: string): Observable<void> {
        return this.http
            .delete<void>(this.contextUrl + '/' + id)
            .pipe(map(data => data));
    }

    /**
    * ProcessoService.findByNome
    *   @description CRUD ACTION findByNome
    *   @param Objectid key Id of the resource Nome to search
    *
    */
    findByNome(id: string): Observable<Processo[]> {
        return this.http
            .get<Processo[]>(this.contextUrl + '/findByNome/' + id)
            .pipe(
                map(response => response)
            );
    }

    /**
    * ProcessoService.get
    *   @description CRUD ACTION get
    *   @param ObjectId id Id 
    *
    */
    get(id: string): Observable<Processo> {
        return this.http
            .get<Processo>(this.contextUrl + '/' + id)
            .pipe(map(data => data));
    }

    /**
    * ProcessoService.list
    *   @description CRUD ACTION list
    *
    */
    list(): Observable<Processo[]> {
        return this.http
            .get<Processo[]>(this.contextUrl)
            .pipe(map(data => data));
    }

    /**
    * ProcessoService.update
    *   @description CRUD ACTION update
    *   @param ObjectId id Id
    *
    */
    update(item: Processo): Observable<Processo> {
        return this.http
            .post<Processo>(this.contextUrl + '/' + item._id, item)
            .pipe(map(data => data));
    }


    // Custom APIs

}
