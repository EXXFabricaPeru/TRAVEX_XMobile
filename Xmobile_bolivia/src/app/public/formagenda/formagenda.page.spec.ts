import { CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { FormagendaPage } from './formagenda.page';

describe('FormagendaPage', () => {
  let component: FormagendaPage;
  let fixture: ComponentFixture<FormagendaPage>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ FormagendaPage ],
      schemas: [CUSTOM_ELEMENTS_SCHEMA],
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(FormagendaPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
