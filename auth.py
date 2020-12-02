# -*- coding: utf-8 -*-
import math

# степенной ряд
def powSeries(x, p1, p2, p3):
    return (p1 + (p2 + p3 * x) * x) * x

# тригонометрический ряд
def trigSeries(x, t2, t4, t6):
    return x + t2 * math.sin(2. * x) + t4 * math.sin(4. * x) + t6 * math.sin(6. * x)

# инициализация эквивалентной сферы
def init(a, f):
    b = a * (1. - f)
    e2 = f * (2. - f)
    R_auth = b * math.sqrt(1. + powSeries(e2, 2./3., 3./5., 4./7.))
    to_auth_2 = powSeries(e2, -1./3., -31./180., -59./560.)
    to_auth_4 = powSeries(e2, 0., 17./360., 61./1260.)
    to_auth_6 = powSeries(e2, 0., 0., -383./45360.)
    return (R_auth, to_auth_2, to_auth_4, to_auth_6)

