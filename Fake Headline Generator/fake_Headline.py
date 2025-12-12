import random

#Subject creation
subject = [
    "Naman Arora",
    "Jonita Mathur",
    "Rahul Dwivedi",
    "Neha Arora",
    "Babar Azam"
]

# Creating actions 
action = [
    "Launches",
    "Cancel",
    "Dances With",
    "Eats",
    "Play with",
    "Sumo"
]

# Creating Objects
place = [
    "at lal kila.",
    "Local train",
    "writing notebook",
    "Road par",
    "Sote huye"
]

while True:
    sub  = random.choice(subject)
    act  = random.choice(action)
    plac = random.choice(place)

    headline = f"BREAKING NEWS: {sub} {act} {plac}"
    print("\n" + headline)

    user_input = input("\nDo you want another headline? (yes/no): ").strip()
    
    if user_input.lower() != "yes":
        break