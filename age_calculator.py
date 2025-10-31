import time
from calendar import isleap

def judge_leap_year(year):
    if isleap(year):
        return True
    else:
        return False
    
def month_days(year, month):
    if month in [1,3,5,7,8,10,12]:
        return 31
    elif month in [4,6,9,11]:
        return 30
    elif month == 2 :
        if judge_leap_year(year):
            return 29
        else:
            return 28

# We will get user details here   
name = input("Enter your name: ")
age = int(input("Enter your age: "))

#Here we get the current local time
localtime = time.localtime(time.time())

#Calculating year, month, days
year = int(age)
month = year * 12 + localtime.tm_mon
day = 0

#Calculating starting and ending year
begin_year = int(localtime.tm_year) - year
end_year = begin_year + year


for y in range(begin_year, end_year):
    if (judge_leap_year(y)):
        day = day + 366
    else:
        day = day + 365

for m in range(1, localtime.tm_mon):
    day = day + month_days(localtime.tm_year, m)

#Days from current month
day = day + localtime.tm_mday


print("\n\t%s's age is %d year or " %(name, year), end="" )
print("%d months or %d days" %(month, day))