import { CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ClientePage } from './cliente.page';

describe('ClientePage', () => {
  let component: ClientePage;
  let fixture: ComponentFixture<ClientePage>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ClientePage ],
      schemas: [CUSTOM_ELEMENTS_SCHEMA],
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ClientePage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
