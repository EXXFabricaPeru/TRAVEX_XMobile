import { CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { PendientesPage } from './pendientes.page';

describe('PendientesPage', () => {
  let component: PendientesPage;
  let fixture: ComponentFixture<PendientesPage>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ PendientesPage ],
      schemas: [CUSTOM_ELEMENTS_SCHEMA],
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PendientesPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
